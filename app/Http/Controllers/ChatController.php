<?php

namespace App\Http\Controllers;

use App\Models\ChatMessage;
use App\Models\ChatSession;
use App\Services\ChatService;
use App\Services\CartService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Log;

class ChatController extends Controller
{
    protected $chatService;
    protected $cartService;

    public function __construct(ChatService $chatService, CartService $cartService)
    {
        $this->chatService = $chatService;
        $this->cartService = $cartService;
    }

    /**
     * Main handler for sending messages
     */
    public function sendMessage(Request $request)
    {
        $request->validate([
            'message' => 'required|string',
        ]);

        $messageText = $request->input('message');
        $user = auth()->user();

        // 1. Xử lý Session (Tách hàm)
        $chatSession = $this->getOrCreateSession($request, $user);
        $sessionToken = $chatSession->session_token;

        // 2. Lưu tin nhắn User
        $chatSession->messages()->create([
            'sender' => 'user',
            'message' => $messageText,
        ]);

        // 3. Tìm kiếm sản phẩm (Context)
        $productsContext = $this->chatService->searchProducts($messageText);

        // 4. Lấy lịch sử chat (Tách hàm)
        $history = $this->getChatHistory($chatSession);

        // 5. Gọi AI và xử lý phản hồi (Tách hàm)
        $aiResult = $this->getAiResponse($messageText, $history, $productsContext, $user, $request);

        // 6. Lưu tin nhắn Bot
        $chatSession->messages()->create([
            'sender' => 'bot',
            'message' => $aiResult['text'],
            'product_context' => $aiResult['products'],
        ]);

        // 7. Trả về kết quả
        return response()->json([
            'response' => $aiResult['text'],
            'products' => $aiResult['products'],
            'redirect_url' => $aiResult['redirect_url'] ?? null,
        ])->cookie('chat_session_token', $sessionToken, 60 * 24 * 30);
    }

    /**
     * Logic tìm hoặc tạo ChatSession
     */
    private function getOrCreateSession(Request $request, $user)
    {
        $sessionToken = $request->cookie('chat_session_token');
        $chatSession = null;

        if ($user) {
            $chatSession = ChatSession::where('user_id', $user->id)->first();
        } elseif ($sessionToken) {
            $chatSession = ChatSession::where('session_token', $sessionToken)->first();
        }

        if (!$chatSession) {
            $sessionToken = (string) Str::uuid();
            $chatSession = ChatSession::create([
                'user_id' => $user ? $user->id : null,
                'session_token' => $sessionToken,
            ]);
        }

        if ($user && !$chatSession->user_id) {
            $chatSession->update(['user_id' => $user->id]);
        }

        return $chatSession;
    }

    /**
     * Logic lấy và format lịch sử chat
     */
    private function getChatHistory(ChatSession $chatSession): array
    {
        return $chatSession->messages()
            ->latest()
            ->take(10)
            ->get()
            ->reverse()
            ->map(function ($msg) {
                return [
                    'sender' => $msg->sender,
                    'message' => $msg->message,
                ];
            })
            ->values()
            ->toArray();
    }

    /**
     * Logic gọi Python API và xử lý kết quả
     */
    private function getAiResponse(string $messageText, array $history, array $productsContext, $user, $request): array
    {
        $botResponseText = "Hệ thống đang bận.";
        $recommendedProducts = [];
        $redirectUrl = null;

        try {
            // Gửi request sang Python...
            $response = Http::timeout(30)->post(env('CHATBOT_URL', 'http://127.0.0.1:5000') . '/process-chat', [
                'message' => $messageText,
                'history' => $history,
                'products_context' => $productsContext,
            ]);

            if ($response->successful()) {
                $data = $response->json();
                $botResponseText = $data['text'] ?? '';
                $action = $data['action'] ?? 'browsing';

                // --- LOGIC MỚI: THÊM VÀO GIỎ & PRE-FILL CHECKOUT ---
                if ($action === 'add_to_cart' && isset($data['cart_data'])) {
                    $cartData = $data['cart_data'];

                    // 1. Thêm sản phẩm vào giỏ hàng (DB)
                    $sessionId = $request->session()->getId(); // Lấy session ID của Laravel
                    $userId = $user ? $user->id : null;

                    $this->cartService->addToCart(
                        $cartData['product_id'],
                        $cartData['quantity'] ?? 1,
                        $userId,
                        $sessionId
                    );

                    // 2. Lưu thông tin khách hàng vào Session Flash để trang Checkout dùng
                    if (isset($cartData['customer_info'])) {
                        session()->put('ai_checkout_info', $cartData['customer_info']);
                        session()->save(); // Đảm bảo lưu ngay lập tức
                    }

                    // 3. Tạo link chuyển hướng
                    $redirectUrl = route('checkout.index');
                    $botResponseText .= "\n\n✅ Đã thêm vào giỏ! Đang chuyển hướng...";
                }

                // Lọc sản phẩm gợi ý...
                if (!empty($data['recommended_products'])) {
                    $recommendedIds = $data['recommended_products'];
                    $recommendedProducts = collect($productsContext)->whereIn('id', $recommendedIds)->values()->toArray();
                }
            }
        } catch (\Exception $e) {
            Log::error('Chatbot Error: ' . $e->getMessage());
        }

        return [
            'text' => $botResponseText,
            'products' => $recommendedProducts,
            'redirect_url' => $redirectUrl // Trả về URL để JS chuyển trang
        ];
    }

    /**
     * Lấy lịch sử chat (Load lại khi F5)
     */
    public function getHistory(Request $request)
    {
        $sessionToken = $request->cookie('chat_session_token');
        $user = auth()->user();

        $session = null;
        if ($user) {
            $session = ChatSession::where('user_id', $user->id)->first();
        } elseif ($sessionToken) {
            $session = ChatSession::where('session_token', $sessionToken)->first();
        }

        if ($session) {
            $messages = $session->messages()
                ->orderBy('created_at', 'asc') // Lấy cũ nhất -> mới nhất
                ->get()
                ->map(function ($msg) {
                    return [
                        'id' => $msg->id,
                        'sender' => $msg->sender,
                        'message' => $msg->message,
                        'products' => $msg->product_context
                    ];
                });
            return response()->json(['messages' => $messages]);
        }

        return response()->json(['messages' => []]);
    }

    /**
     * Xóa lịch sử chat
     */
    public function clearHistory(Request $request)
    {
        $user = auth()->user();
        $sessionToken = $request->cookie('chat_session_token');

        $session = null;
        if ($user) {
            $session = ChatSession::where('user_id', $user->id)->first();
        } elseif ($sessionToken) {
            $session = ChatSession::where('session_token', $sessionToken)->first();
        }

        if ($session) {
            // Xóa toàn bộ tin nhắn trong DB
            $session->messages()->delete();
            // Xóa luôn session chat để reset hoàn toàn
            $session->delete();
        }

        // Trả về response và YÊU CẦU TRÌNH DUYỆT XÓA COOKIE
        return response()->json(['status' => 'success'])
            ->withoutCookie('chat_session_token');
    }
}