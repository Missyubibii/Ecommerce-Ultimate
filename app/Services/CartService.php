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
     * Lấy giỏ hàng của User hoặc Guest
     */
    public function getCart($userId = null, $sessionId = null)
    {
        $query = CartItem::with(['product.product_images']);

        if ($userId) {
            $query->where('user_id', $userId);
        } else {
            $query->where('session_id', $sessionId)->whereNull('user_id');
        }

        $items = $query->get();

        // Tính toán tổng tiền động
        $subtotal = 0;
        foreach ($items as $item) {
            // Giá ưu tiên: market_price nếu cần, ở đây dùng price
            $price = $item->product->price ?? 0;
            $item->current_price = $price;
            $item->total = $price * $item->quantity;
            $subtotal += $item->total;
        }

        return [
            'items' => $items,
            'count' => $items->sum('quantity'),
            'subtotal' => $subtotal
        ];
    }

    /**
     * Thêm sản phẩm vào giỏ
     */
    public function addToCart($userId, $sessionId, $productId, $quantity = 1)
    {
        $product = Product::findOrFail($productId);

        if ($product->quantity < $quantity) {
            throw new \Exception("Sản phẩm chỉ còn {$product->quantity} hàng trong kho.");
        }

        // Tìm item đã tồn tại chưa
        $query = CartItem::where('product_id', $productId);
        if ($userId) {
            $query->where('user_id', $userId);
        } else {
            $query->where('session_id', $sessionId)->whereNull('user_id');
        }

        $cartItem = $query->first();

        if ($cartItem) {
            // Update quantity
            $cartItem->quantity += $quantity;
            $cartItem->save();
        } else {
            // Create new
            CartItem::create([
                'user_id' => $userId,
                'session_id' => $userId ? null : $sessionId, // Nếu là user thì không cần lưu session_id rác
                'product_id' => $productId,
                'quantity' => $quantity
            ]);
        }

        return $this->getCart($userId, $sessionId);
    }

    /**
     * Cập nhật số lượng
     */
    public function updateQty($itemId, $quantity)
    {
        $item = CartItem::findOrFail($itemId);

        if ($quantity <= 0) {
            $item->delete();
        } else {
            // Check stock
            if ($item->product->quantity < $quantity) {
                throw new \Exception("Kho không đủ hàng.");
            }
            $item->quantity = $quantity;
            $item->save();
        }
        // Trả về cart mới nhất để update UI
        $userId = Auth::id();
        $sessionId = Session::getId();
        return $this->getCart($userId, $sessionId);
    }

    /**
     * Lấy tổng quan giỏ hàng (Số lượng, Tổng tiền)
     * Thường dùng cho Mini Cart header hoặc AJAX response
     */
    public function getCartTotals($userId = null, $sessionId = null)
    {
        // Tái sử dụng getCart để đảm bảo logic tính giá (ưu tiên giá mới nhất từ DB) giống nhau
        // Nếu muốn tối ưu hiệu năng hơn (không load ảnh), có thể viết query riêng
        $cartData = $this->getCart($userId, $sessionId);

        return [
            'count' => $cartData['count'],
            'subtotal' => $cartData['subtotal'],
            // Tại đây có thể mở rộng logic tính Tax, Discount, Shipping nếu cần
            'total' => $cartData['subtotal'],
            'formatted_subtotal' => number_format($cartData['subtotal'], 0, ',', '.') . 'đ'
        ];
    }

    /**
     * Xóa 1 item
     */
    public function removeItem($itemId)
    {
        CartItem::destroy($itemId);

        $userId = Auth::id();
        $sessionId = Session::getId();
        return $this->getCart($userId, $sessionId);
    }

    /**
     * Merge giỏ hàng từ Session sang User khi Login
     */
    public function mergeCart($sessionId, $userId)
    {
        // 1. Lấy giỏ hàng Guest
        $guestItems = CartItem::where('session_id', $sessionId)->whereNull('user_id')->get();

        foreach ($guestItems as $guestItem) {
            // 2. Kiểm tra xem User đã có sản phẩm này trong giỏ chưa
            $userItem = CartItem::where('user_id', $userId)
                ->where('product_id', $guestItem->product_id)
                ->first();

            if ($userItem) {
                // Cộng dồn số lượng
                $userItem->quantity += $guestItem->quantity;
                $userItem->save();
                // Xóa item guest cũ
                $guestItem->delete();
            } else {
                // Chuyển quyền sở hữu từ session sang user
                $guestItem->user_id = $userId;
                $guestItem->session_id = null; // Clear session id để sạch data
                $guestItem->save();
            }
        }
    }

    // =========================================================================
    // SECTION 2: ADMIN METHODS (Phục vụ CartController bạn cung cấp)
    // =========================================================================

    /**
     * Lấy danh sách các giỏ hàng đang active để hiển thị Admin Table
     * Logic: Group by SessionID hoặc UserID để đếm tổng quát
     */
    public function getAdminCartsListing($perPage = 10)
    {
        // Sử dụng Eloquent để group và select raw
        // Lưu ý: Cần config database 'strict' => false trong config/database.php nếu MySQL báo lỗi Group By,
        // hoặc liệt kê đầy đủ các cột trong Group By.
        // Ở đây ta select các cột định danh để hiển thị.

        return CartItem::query()
            ->select(
                'session_id',
                'user_id',
                DB::raw('COUNT(id) as total_unique_items'), // Số loại sản phẩm
                DB::raw('SUM(quantity) as total_quantity'), // Tổng số lượng
                DB::raw('MAX(updated_at) as last_active')   // Thời gian cập nhật cuối
            )
            ->with('user') // Eager load user để hiển thị tên
            ->groupBy('session_id', 'user_id')
            ->orderByDesc('last_active')
            ->paginate($perPage);
    }

    /**
     * Lấy chi tiết một giỏ hàng cụ thể cho Admin xem
     */
    public function getAdminCartDetails($userId, $sessionId)
    {
        $query = CartItem::with(['product', 'user']);

        if ($userId) {
            $query->where('user_id', $userId);
        } elseif ($sessionId) {
            $query->where('session_id', $sessionId)->whereNull('user_id');
        } else {
            // Trường hợp không có param nào, trả về collection rỗng
            return collect([]);
        }

        $items = $query->get();

        // Map thêm thuộc tính 'total' để Controller dùng $items->sum('total')
        $items->transform(function ($item) {
            $price = $item->product->price ?? 0;
            $item->price = $price; // Gán giá tại thời điểm xem
            $item->total = $price * $item->quantity;
            return $item;
        });

        return $items;
    }

    /**
     * Admin xóa giỏ hàng (Clear cart)
     */
    public function clearCartByAdmin($userId, $sessionId)
    {
        $query = CartItem::query();

        if ($userId) {
            $query->where('user_id', $userId);
        } elseif ($sessionId) {
            $query->where('session_id', $sessionId); // Admin clear có thể xóa thẳng tay session
        } else {
            return false;
        }

        return $query->delete();
    }
}
