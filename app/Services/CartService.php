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
        // 1. Chỉ cần with('product') vì cột 'image' nằm ngay trong bảng products
        $query = CartItem::with(['product']);

        if ($userId) {
            $query->where('user_id', $userId);
        } else {
            $query->where('session_id', $sessionId)->whereNull('user_id');
        }

        $items = $query->get();
        $subtotal = 0;

        $items->each(function ($item) use (&$subtotal) {
            $product = $item->product;

            $img = $product->image;
            $finalImageUrl = 'https://placehold.co/100?text=No+Image'; // Mặc định

            if (!empty($img)) {
                if (filter_var($img, FILTER_VALIDATE_URL)) {
                    // 1. Nếu là link online (http...)
                    $finalImageUrl = $img;
                } elseif (file_exists(public_path($img))) {
                    // 2. Nếu file nằm trực tiếp trong folder public/ (VD: public/images/...)
                    $finalImageUrl = asset($img);
                } else {
                    // 3. Nếu không tìm thấy ở public, thử tìm trong storage (VD: public/storage/images/...)
                    // Code này tự động thêm 'storage/' nếu đường dẫn trong DB chưa có
                    $pathWithStorage = str_starts_with($img, 'storage/') ? $img : 'storage/' . $img;
                    $finalImageUrl = asset($pathWithStorage);
                }
            }
            $item->product->image = $finalImageUrl;

            // Mặc định là OK
            $item->is_available = true;
            $item->message = '';

            // Check 1: Sản phẩm không tồn tại
            if (!$product) {
                $item->is_available = false;
                $item->message = 'Sản phẩm không tồn tại';
                return; // Dừng check, không tính tiền
            }

            // Check 2: Trạng thái (Dựa trên status='active' và is_active=1)
            if ($product->status !== 'active' || $product->is_active != 1) {
                $item->is_available = false;
                $item->message = 'Ngừng kinh doanh';
            }

            // Check 3: Hết hàng (quantity = 0)
            elseif ($product->quantity <= 0) {
                $item->is_available = false;
                $item->message = 'Đã hết hàng';
            }

            // Check 4: Mua quá số lượng kho
            elseif ($item->quantity > $product->quantity) {
                $item->is_available = false;
                $item->message = "Kho chỉ còn {$product->quantity} chiếc.";
            }

            // Tính tiền
            $item->total = ($product->price ?? 0) * $item->quantity;

            // Chỉ cộng tổng tiền nếu sản phẩm HỢP LỆ (is_available = true)
            if ($item->is_available) {
                $subtotal += $item->total;
            }
        });

        return [
            'items' => $items,
            // Đếm tổng số lượng (bao gồm cả hàng lỗi để khách thấy mà xóa)
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

    public function getCartTotals($identifier): float
    {
        // Luôn join với bảng products để lấy giá mới nhất
        // Tránh trường hợp sản phẩm đã đổi giá nhưng trong giỏ vẫn lưu giá cũ
        return CartItem::where($identifier)
            ->join('products', 'cart_items.product_id', '=', 'products.id')
            ->sum(DB::raw('cart_items.quantity * products.price'));
    }

    // =========================================================================
    // SECTION 2: ADMIN METHODS
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
