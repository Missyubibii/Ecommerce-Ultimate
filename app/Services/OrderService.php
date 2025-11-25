<?php

namespace App\Services;

use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Product;
use App\Models\Address;
use App\Models\Payment;  // Module G
use App\Models\Shipment; // Module H
use App\Models\CartItem;
use App\Services\CartService;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Exception;

class OrderService
{
    protected $cartService;

    public function __construct(CartService $cartService)
    {
        $this->cartService = $cartService;
    }

    /**
     * TRANSACTION: Xử lý đặt hàng (Tạo Order + Items + Payment + Shipment)
     */
    public function placeOrder($user, array $payload): Order
    {
        // 1. Lấy dữ liệu giỏ hàng
        $cartData = $this->cartService->getCartTotals();
        $items = $cartData['items'];

        if ($items->isEmpty()) {
            throw new Exception("Giỏ hàng trống, không thể đặt hàng.");
        }

        // 2. Resolve địa chỉ
        $shippingAddress = $this->resolveAddress($user, $payload);

        // 3. Bắt đầu Transaction (Atomic)
        return DB::transaction(function () use ($user, $items, $payload, $cartData, $shippingAddress) {

            // A. Trừ kho (Module C logic)
            foreach ($items as $cartItem) {
                // Lock dòng product để tránh race condition
                $product = Product::where('id', $cartItem->product_id)->lockForUpdate()->first();

                if (!$product) throw new Exception("Sản phẩm ID {$cartItem->product_id} không tồn tại.");
                if ($product->quantity < $cartItem->quantity) throw new Exception("Sản phẩm '{$product->name}' không đủ hàng.");

                $product->quantity -= $cartItem->quantity;
                // Nếu hết hàng, có thể set status = draft hoặc out_of_stock tùy logic
                // if ($product->quantity === 0) $product->status = 'draft';
                $product->save();
            }

            // B. Tạo Order (Module F)
            $order = Order::create([
                'order_number' => 'ORD-' . strtoupper(Str::random(10)),
                'user_id' => $user ? $user->id : null,
                'status' => 'pending',
                'total_amount' => $cartData['subtotal'],
                'shipping_amount' => 0, // Mở rộng logic tính phí ship sau
                'payment_method' => $payload['payment_method'] ?? 'cod',
                'shipping_address' => $shippingAddress,
                'metadata' => [
                    'note' => $payload['note'] ?? null,
                    'ip' => request()->ip(),
                    'user_agent' => request()->userAgent()
                ]
            ]);

            // C. Tạo Order Items (Module F)
            foreach ($items as $item) {
                $order->items()->create([
                    'product_id' => $item->product_id,
                    'quantity' => $item->quantity,
                    'unit_price' => $item->product->price,
                    'subtotal' => $item->total,
                    'product_snapshot' => [
                        'name' => $item->product->name,
                        'sku' => $item->product->sku,
                        'slug' => $item->product->slug,
                        'image' => $item->product->image
                    ]
                ]);
            }

            // D. Tạo Payment (Module G) - Init: pending
            Payment::create([
                'order_id' => $order->id,
                'method' => $payload['payment_method'] ?? 'cod',
                'amount' => $order->total_amount,
                'status' => 'pending'
            ]);

            // E. Tạo Shipment (Module H) - Init: pending
            Shipment::create([
                'order_id' => $order->id,
                'status' => 'pending',
                'cost' => 0 // Chi phí vận chuyển thực tế (shop chịu hoặc khách chịu)
            ]);

            // F. Dọn dẹp giỏ hàng (Module D)
            if ($user) {
                CartItem::where('user_id', $user->id)->delete();
            } else {
                CartItem::where('session_id', session()->getId())->delete();
            }

            return $order;
        });
    }

    // --- Admin & Helper Methods ---

    protected function resolveAddress($user, $payload)
    {
        if (isset($payload['address_id']) && $user) {
            $address = Address::where('user_id', $user->id)->where('id', $payload['address_id'])->first();
            if (!$address) throw new Exception("Địa chỉ không hợp lệ.");
            return $address->toArray();
        }
        if (isset($payload['new_address'])) {
            return $payload['new_address'];
        }
        throw new Exception("Vui lòng cung cấp địa chỉ giao hàng.");
    }

    public function getAdminOrders(array $filters = [], $perPage = 15)
    {
        // Eager load cả payment và shipment để hiển thị danh sách admin
        $query = Order::with(['user', 'payment', 'shipment'])->orderByDesc('created_at');

        if (!empty($filters['status'])) {
            $query->where('status', $filters['status']);
        }
        if (!empty($filters['q'])) {
            $query->where('order_number', 'like', '%' . $filters['q'] . '%');
        }
        if (!empty($filters['date_from'])) {
            $query->whereDate('created_at', '>=', $filters['date_from']);
        }
        if (!empty($filters['date_to'])) {
            $query->whereDate('created_at', '<=', $filters['date_to']);
        }

        return $query->paginate($perPage)->withQueryString();
    }

    public function getOrderDetails($id)
    {
        return Order::with(['items', 'user', 'payment', 'shipment'])->findOrFail($id);
    }

    public function updateOrderStatus($id, $newStatus)
    {
        $order = Order::findOrFail($id);
        $order->status = $newStatus;
        $order->save();

        // Logic tự động (Optional): Nếu đơn hoàn thành -> set payment/shipment completed
        if ($newStatus === 'completed') {
            if ($order->payment && $order->payment->status !== 'paid') {
                $order->payment->update(['status' => 'paid', 'paid_at' => now()]);
            }
            if ($order->shipment && $order->shipment->status !== 'delivered') {
                $order->shipment->update(['status' => 'delivered', 'delivered_at' => now()]);
            }
        }

        return $order;
    }

    public function updatePaymentStatus($paymentId, $status)
    {
        $payment = Payment::findOrFail($paymentId);
        $data = ['status' => $status];
        if ($status == 'paid') $data['paid_at'] = now();

        $payment->update($data);

        // Sync ngược lại Order: Nếu đã thanh toán -> order processing
        if ($status == 'paid' && $payment->order->status == 'pending') {
            $payment->order->update(['status' => 'processing']);
        }
    }

    public function updateShipmentInfo($shipmentId, $carrier, $tracking, $status)
    {
        $shipment = Shipment::findOrFail($shipmentId);
        $data = [
            'carrier' => $carrier,
            'tracking_number' => $tracking,
            'status' => $status
        ];
        if ($status == 'in_transit') $data['shipped_at'] = now();
        if ($status == 'delivered') $data['delivered_at'] = now();

        $shipment->update($data);

        // Sync ngược lại Order
        if (in_array($status, ['picked_up', 'in_transit']) && $shipment->order->status == 'processing') {
            $shipment->order->update(['status' => 'shipped']);
        }
    }
}
