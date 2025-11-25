<?php

namespace App\Services;

use App\Models\CartItem;
use App\Models\Product;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Collection;

class CartService
{
    /**
     * Lấy định danh giỏ hàng hiện tại (User ID hoặc Session ID)
     */
    protected function getCartIdentifier(): array
    {
        if (Auth::check()) {
            return ['user_id' => Auth::id()];
        }

        // Nếu chưa có session_id cho cart, tạo mới
        if (!Session::has('cart_session_id')) {
            Session::put('cart_session_id', Session::getId());
        }

        return ['session_id' => Session::get('cart_session_id')];
    }

    /**
     * Lấy danh sách items trong giỏ
     */
    public function getCart(): Collection
    {
        $identifier = $this->getCartIdentifier();

        return CartItem::with(['product' => function ($q) {
            // Select các field cần thiết để tối ưu
            $q->select('id', 'name', 'slug', 'price', 'image', 'quantity as stock', 'sku');
        }])
            ->where($identifier)
            ->get()
            // Map thêm subtotal động dựa trên giá mới nhất từ DB
            ->map(function ($item) {
                $item->total = $item->quantity * $item->product->price;
                return $item;
            });
    }

    /**
     * Tính tổng tiền giỏ hàng
     */
    public function getCartTotals(): array
    {
        $cart = $this->getCart();
        $subtotal = $cart->sum('total');
        $count = $cart->sum('quantity');

        return [
            'subtotal' => $subtotal,
            'count' => $count,
            'items' => $cart
        ];
    }

    /**
     * Thêm vào giỏ hàng
     */
    public function addToCart(int $productId, int $quantity = 1): CartItem
    {
        $product = Product::findOrFail($productId);

        // Check tồn kho
        if ($product->quantity < $quantity) {
            throw new \Exception("Sản phẩm {$product->name} chỉ còn {$product->quantity} sản phẩm.");
        }

        $identifier = $this->getCartIdentifier();

        // Tìm xem item đã có trong giỏ chưa
        $item = CartItem::where($identifier)->where('product_id', $productId)->first();

        if ($item) {
            $newQty = $item->quantity + $quantity;
            if ($product->quantity < $newQty) {
                throw new \Exception("Kho không đủ hàng. Tổng số lượng yêu cầu: $newQty");
            }
            $item->quantity = $newQty;
            $item->save();
        } else {
            $item = CartItem::create(array_merge($identifier, [
                'product_id' => $productId,
                'quantity' => $quantity
            ]));
        }

        return $item;
    }

    /**
     * Cập nhật số lượng
     */
    public function updateQuantity(int $itemId, int $quantity)
    {
        if ($quantity <= 0) {
            return $this->removeItem($itemId);
        }

        $item = CartItem::findOrFail($itemId);

        // Check quyền sở hữu item
        $this->verifyOwnership($item);

        // Check stock real-time
        if ($item->product->quantity < $quantity) {
            throw new \Exception("Kho không đủ hàng.");
        }

        $item->quantity = $quantity;
        $item->save();

        return $item;
    }

    /**
     * Xóa item
     */
    public function removeItem(int $itemId)
    {
        $item = CartItem::findOrFail($itemId);
        $this->verifyOwnership($item);
        $item->delete();
    }

    /**
     * Merge giỏ hàng từ Session sang User khi Login
     * Gọi hàm này trong Login Listener (Authenticated Event)
     */
    public function mergeCart(string $sessionId, int $userId)
    {
        DB::transaction(function () use ($sessionId, $userId) {
            $sessionItems = CartItem::where('session_id', $sessionId)->get();

            foreach ($sessionItems as $sItem) {
                // Check xem user đã có item này chưa
                $userItem = CartItem::where('user_id', $userId)
                    ->where('product_id', $sItem->product_id)
                    ->first();

                if ($userItem) {
                    // Cộng dồn số lượng, nhưng không vượt quá stock
                    $productStock = $sItem->product->quantity;
                    $newQty = $userItem->quantity + $sItem->quantity;

                    $userItem->quantity = min($newQty, $productStock);
                    $userItem->save();
                } else {
                    // Chuyển item sang cho user
                    $sItem->session_id = null;
                    $sItem->user_id = $userId;
                    $sItem->save();
                }
            }

            // Xóa các item session cũ còn sót lại (nếu đã được xử lý ở trên thì dòng này safe)
            CartItem::where('session_id', $sessionId)->delete();
        });
    }

    // Helper check quyền
    protected function verifyOwnership($item)
    {
        $identifier = $this->getCartIdentifier();
        if (isset($identifier['user_id']) && $item->user_id !== $identifier['user_id']) {
            abort(403);
        }
        if (isset($identifier['session_id']) && $item->session_id !== $identifier['session_id']) {
            abort(403);
        }
    }

    /**
     * ADMIN: Lấy danh sách tất cả giỏ hàng (nhóm theo User hoặc Session)
     */
    public function getAdminCartsListing($perPage = 20)
    {
        // Nhóm các items theo định danh người dùng để tạo thành "danh sách giỏ hàng"
        // Cần join với products để tính tổng tiền (vì giá nằm ở bảng products)
        return CartItem::query()
            ->join('products', 'cart_items.product_id', '=', 'products.id')
            ->selectRaw('
                cart_items.user_id,
                cart_items.session_id,
                count(cart_items.id) as distinct_items,
                sum(cart_items.quantity) as total_quantity,
                sum(cart_items.quantity * products.price) as cart_total,
                max(cart_items.updated_at) as last_updated
            ')
            ->groupBy('cart_items.user_id', 'cart_items.session_id')
            ->with('user') // Eager load thông tin user
            ->orderByDesc('last_updated')
            ->paginate($perPage);
    }

    /**
     * ADMIN: Xem chi tiết một giỏ hàng cụ thể
     */
    public function getAdminCartDetails($userId, $sessionId)
    {
        $query = CartItem::with(['product', 'user']);

        if ($userId) {
            $query->where('user_id', $userId);
        } elseif ($sessionId) {
            $query->where('session_id', $sessionId);
        } else {
            return collect([]);
        }

        return $query->get()->map(function ($item) {
            $item->total = $item->quantity * $item->product->price;
            return $item;
        });
    }

    /**
     * ADMIN: Xóa toàn bộ giỏ hàng của khách (Cleanup)
     */
    public function clearCartByAdmin($userId, $sessionId)
    {
        $query = CartItem::query();
        if ($userId) {
            $query->where('user_id', $userId);
        } elseif ($sessionId) {
            $query->where('session_id', $sessionId);
        }
        return $query->delete();
    }
}
