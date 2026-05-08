<?php

namespace App\Services;

use App\Models\CartItem;
use App\Models\Product;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Auth;

class CartService
{
    // =========================================================================
    // SECTION 1: CORE FRONTEND METHODS (Logic giỏ hàng cho người dùng)
    // =========================================================================

    /**
     * 1. Lấy thông tin giỏ hàng hiện tại (Dành cho cả thành viên và khách)
     */
    public function getCart($userId = null, $sessionId = null)
    {
        // Khởi tạo truy vấn lấy sản phẩm kèm theo (Eager Loading)
        $query = CartItem::with(['product']);

        // Phân loại: Ưu tiên lấy theo User ID, nếu không có thì lấy theo Session ID (Khách)
        if ($userId) {
            $query->where('user_id', $userId);
        } else {
            $query->where('session_id', $sessionId)->whereNull('user_id');
        }

        $items = $query->get();
        $subtotal = 0;

        // Duyệt qua từng sản phẩm để xử lý logic hình ảnh, trạng thái kho và tính tiền
        $items->each(function ($item) use (&$subtotal) {
            $product = $item->product;

            // --- Xử lý logic hiển thị hình ảnh sản phẩm ---
            $img = $product->image;
            $finalImageUrl = 'https://placehold.co/100?text=No+Image'; // Ảnh mặc định nếu trống

            if (!empty($img)) {
                if (filter_var($img, FILTER_VALIDATE_URL)) {
                    // Trường hợp 1: Link ảnh từ web bên ngoài (URL trực tiếp)
                    $finalImageUrl = $img;
                } elseif (file_exists(public_path($img))) {
                    // Trường hợp 2: File ảnh nằm trong thư mục public gốc
                    $finalImageUrl = asset($img);
                } else {
                    // Trường hợp 3: Thử tìm trong thư mục storage/app/public
                    $pathWithStorage = str_starts_with($img, 'storage/') ? $img : 'storage/' . $img;
                    $finalImageUrl = asset($pathWithStorage);
                }
            }
            $item->product->image = $finalImageUrl;

            // --- Kiểm tra tính hợp lệ của sản phẩm trong giỏ ---
            $item->is_available = true;
            $item->message = '';

            // Kiểm tra 1: Sản phẩm đã bị xóa khỏi hệ thống
            if (!$product) {
                $item->is_available = false;
                $item->message = 'Sản phẩm không tồn tại';
                return;
            }

            // Kiểm tra 2: Sản phẩm đã ngừng kinh doanh (Status inactive)
            if ($product->status !== 'active' || $product->is_active != 1) {
                $item->is_available = false;
                $item->message = 'Ngừng kinh doanh';
            }

            // Kiểm tra 3: Sản phẩm trong kho đã hết hàng (Số lượng = 0)
            elseif ($product->quantity <= 0) {
                $item->is_available = false;
                $item->message = 'Đã hết hàng';
            }

            // Kiểm tra 4: Số lượng khách mua vượt quá số lượng còn lại trong kho
            elseif ($item->quantity > $product->quantity) {
                $item->is_available = false;
                $item->message = "Kho chỉ còn {$product->quantity} chiếc.";
            }

            // --- Tính toán tổng tiền của từng dòng sản phẩm ---
            $item->total = ($product->price ?? 0) * $item->quantity;

            // Chỉ cộng vào tổng đơn hàng nếu sản phẩm đó còn hàng và đang bán (is_available = true)
            if ($item->is_available) {
                $subtotal += $item->total;
            }
        });

        return [
            'items' => $items,
            'count' => $items->sum('quantity'), // Tổng số lượng món hàng trong giỏ
            'subtotal' => $subtotal             // Tổng số tiền tạm tính
        ];
    }

    /**
     * 2. Thêm một sản phẩm mới vào giỏ hàng
     */
    public function addToCart($userId, $sessionId, $productId, $quantity = 1)
    {
        $product = Product::findOrFail($productId);

        // Kiểm tra kho trước khi cho phép thêm vào giỏ
        if ($product->quantity < $quantity) {
            throw new \Exception("Sản phẩm chỉ còn {$product->quantity} hàng trong kho.");
        }

        // Kiểm tra xem sản phẩm này đã có trong giỏ chưa
        $query = CartItem::where('product_id', $productId);
        if ($userId) {
            $query->where('user_id', $userId);
        } else {
            $query->where('session_id', $sessionId)->whereNull('user_id');
        }

        $cartItem = $query->first();

        if ($cartItem) {
            // Nếu đã có: Chỉ cập nhật thêm số lượng
            $cartItem->quantity += $quantity;
            $cartItem->save();
        } else {
            // Nếu chưa có: Tạo bản ghi mới trong giỏ hàng
            CartItem::create([
                'user_id' => $userId,
                'session_id' => $userId ? null : $sessionId,
                'product_id' => $productId,
                'quantity' => $quantity
            ]);
        }

        // Trả về dữ liệu giỏ hàng mới nhất để cập nhật UI
        return $this->getCart($userId, $sessionId);
    }

    /**
     * 3. Thay đổi số lượng của một sản phẩm trong giỏ
     */
    public function updateQty($itemId, $quantity)
    {
        $item = CartItem::findOrFail($itemId);

        if ($quantity <= 0) {
            // Nếu số lượng về 0 hoặc nhỏ hơn: Xóa sản phẩm khỏi giỏ
            $item->delete();
        } else {
            // Kiểm tra kho: Đảm bảo số lượng yêu cầu không vượt quá số lượng thực tế
            if ($item->product->quantity < $quantity) {
                throw new \Exception("Kho không đủ hàng.");
            }
            $item->quantity = $quantity;
            $item->save();
        }

        $userId = Auth::id();
        $sessionId = Session::getId();
        return $this->getCart($userId, $sessionId);
    }

    /**
     * 4. Xóa hoàn toàn một dòng sản phẩm khỏi giỏ hàng
     */
    public function removeItem($itemId)
    {
        CartItem::destroy($itemId);

        $userId = Auth::id();
        $sessionId = Session::getId();
        return $this->getCart($userId, $sessionId);
    }

    /**
     * 5. Hợp nhất giỏ hàng (Chuyển sản phẩm từ khách vãng lai sang User sau khi đăng nhập)
     */
    public function mergeCart($sessionId, $userId)
    {
        // Lấy tất cả sản phẩm đang lưu theo Session ID của khách
        $guestItems = CartItem::where('session_id', $sessionId)->whereNull('user_id')->get();

        foreach ($guestItems as $guestItem) {
            // Kiểm tra xem User đã có sản phẩm này trong giỏ của họ chưa
            $userItem = CartItem::where('user_id', $userId)
                ->where('product_id', $guestItem->product_id)
                ->first();

            if ($userItem) {
                // Nếu đã có: Cộng dồn số lượng từ giỏ khách vào giỏ User và xóa bản ghi khách
                $userItem->quantity += $guestItem->quantity;
                $userItem->save();
                $guestItem->delete();
            } else {
                // Nếu chưa có: Chuyển quyền sở hữu trực tiếp (Cập nhật user_id và xóa session_id)
                $guestItem->user_id = $userId;
                $guestItem->session_id = null;
                $guestItem->save();
            }
        }
    }

    /**
     * 6. Lấy tổng tiền giỏ hàng theo định danh (Dùng cho Checkout/Order)
     */
    public function getCartTotals($identifier): float
    {
        // Join với bảng products để đảm bảo lấy giá bán mới nhất tại thời điểm thanh toán
        return CartItem::where($identifier)
            ->join('products', 'cart_items.product_id', '=', 'products.id')
            ->sum(DB::raw('cart_items.quantity * products.price'));
    }

    // =========================================================================
    // SECTION 2: ADMIN METHODS (Dành cho trang quản trị)
    // =========================================================================

    /**
     * 7. [Admin] Lấy danh sách tổng hợp các giỏ hàng đang hoạt động trên hệ thống
     */
    public function getAdminCartsListing($perPage = 10)
    {
        return CartItem::query()
            ->select(
                'session_id',
                'user_id',
                DB::raw('COUNT(id) as total_unique_items'), // Đếm số loại sản phẩm (SKU)
                DB::raw('SUM(quantity) as total_quantity'), // Tổng số lượng sản phẩm
                DB::raw('MAX(updated_at) as last_active')   // Thời điểm cuối cùng giỏ hàng được cập nhật
            )
            ->with('user') // Lấy tên người dùng nếu có
            ->groupBy('session_id', 'user_id')
            ->orderByDesc('last_active')
            ->paginate($perPage);
    }

    /**
     * 8. [Admin] Xem chi tiết các sản phẩm bên trong một giỏ hàng cụ thể
     */
    public function getAdminCartDetails($userId, $sessionId)
    {
        $query = CartItem::with(['product', 'user']);

        if ($userId) {
            $query->where('user_id', $userId);
        } elseif ($sessionId) {
            $query->where('session_id', $sessionId)->whereNull('user_id');
        } else {
            return collect([]);
        }

        $items = $query->get();

        // Chuẩn bị dữ liệu hiển thị giá và tổng tiền tại thời điểm Admin đang xem
        $items->transform(function ($item) {
            $price = $item->product->price ?? 0;
            $item->price = $price;
            $item->total = $price * $item->quantity;
            return $item;
        });

        return $items;
    }

    /**
     * 9. [Admin] Xóa bỏ toàn bộ giỏ hàng của một người dùng hoặc một phiên làm việc
     */
    public function clearCartByAdmin($userId, $sessionId)
    {
        $query = CartItem::query();

        if ($userId) {
            $query->where('user_id', $userId);
        } elseif ($sessionId) {
            $query->where('session_id', $sessionId);
        } else {
            return false;
        }

        return $query->delete();
    }
}