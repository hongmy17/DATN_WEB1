<?php

namespace App\Http\Controllers;

use App\Mail\OrderConfirmationMail;
use App\Models\Coupon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Mail;
use Carbon\Carbon;

use App\Models\Order;
use App\Models\OrderItem;
use App\Models\ProductVariant;
use Illuminate\Support\Facades\DB;

class CheckoutController extends Controller
{
    public function index()
    {
        $defaultAddress = null;
        $addresses = collect();

        if (Auth::check()) {
            $addresses = Auth::user()->addresses()->latest()->get();
            $defaultAddress = $addresses->firstWhere('is_default', true)
                ?? $addresses->first();
        }

        $coupons = Coupon::where('status', 1)
            ->where('start_date', '<=', Carbon::now())
            ->where('end_date', '>=', Carbon::now())
            ->where(function ($q) {
                $q->whereNull('max_usage')
                    ->orWhereColumn('used_count', '<', 'max_usage');
            })
            ->get();

        return view('pages.checkout.index', compact('defaultAddress', 'addresses', 'coupons'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'receiver_name'  => 'required_without:address_id|nullable|string|max:100',
            'receiver_phone' => 'required_without:address_id|nullable|string|max:15',
            'province'       => 'required_without:address_id|nullable|string|max:100',
            'district'       => 'required_without:address_id|nullable|string|max:100',
            'ward'           => 'required_without:address_id|nullable|string|max:100',
            'address_detail' => 'required_without:address_id|nullable|string|max:255',
            'address_id'     => 'nullable|integer|exists:user_addresses,id',
            'cart'           => 'required|array|min:1',
            'cart.*.id'      => 'required|integer',
            'cart.*.qty'     => 'required|integer|min:1',
        ]);

        // Nếu chọn địa chỉ đã lưu → lấy thông tin từ DB
        if ($request->address_id) {
            $savedAddr = \App\Models\UserAddress::find($request->address_id);
            if ($savedAddr && $savedAddr->user_id === Auth::id()) {
                $request->merge([
                    'receiver_name'  => $savedAddr->receiver_name,
                    'receiver_phone' => $savedAddr->receiver_phone,
                    'province'       => $savedAddr->province,
                    'district'       => $savedAddr->district,
                    'ward'           => $savedAddr->ward,
                    'address_detail' => $savedAddr->address_detail,
                ]);
            }
        }

        try {
            $order = DB::transaction(function () use ($request) {
                $cart = collect($request->cart);

                // Lấy variants theo ID từ giỏ hàng
                // Cart gửi lên id = variant_id (từ variantMap trong show.blade.php)
                $variantIds = $cart->pluck('id')->toArray();

                $variants = ProductVariant::with(['product', 'attributeValues.attribute'])
                    ->whereIn('id', $variantIds)  // FIX: dùng id (variant_id) thay vì product_id
                    ->get()
                    ->keyBy('id');               // FIX: keyBy 'id' (variant_id)

                // ── Bước 1: Kiểm tra kho + tính tổng tiền ─────────────────
                $subtotal = 0;
                $now = now();

                foreach ($cart as $item) {
                    $variant = $variants[$item['id']] ?? null;

                    if (!$variant) {
                        throw new \Exception(
                            'Sản phẩm trong giỏ hàng không tồn tại.'
                        );
                    }

                    // Kiểm tra kho (chỉ với hàng quản lý kho)
                    if ($variant->manage_stock && $variant->stock_quantity < $item['qty']) {
                        throw new \Exception(
                            "Sản phẩm \"{$variant->product->name}\" không đủ tồn kho "
                                . "(còn {$variant->stock_quantity}, cần {$item['qty']})."
                        );
                    }

                    // FIX: tính giá đúng theo sale_price + thời gian
                    $unitPrice = $this->getEffectivePrice($variant, $now);
                    $subtotal += $unitPrice * $item['qty'];
                }

                // ── Bước 2: Xử lý coupon ──────────────────────────────────
                $discountAmount = 0;
                $coupon = null;
                $couponCode = $request->coupon_code ?? session('coupon_code');

                if ($couponCode) {
                    $coupon = Coupon::where('coupon_code', strtoupper($couponCode))->first();
                    if ($coupon && $coupon->isValid($subtotal)) {
                        $discountAmount = $coupon->calcDiscount($subtotal);
                        $coupon->increment('used_count');
                    } else {
                        $coupon = null;
                    }
                }

                $totalAmount = max($subtotal - $discountAmount, 0);
                $shippingAddress = collect([
                    $request->address_detail,
                    $request->ward,
                    $request->district,
                    $request->province,
                ])->filter()->implode(', ');

                // ── Bước 3: Tạo đơn hàng ──────────────────────────────────
                $order = Order::create([
                    'user_id'          => Auth::id(),
                    'address_id'       => $request->address_id,
                    'coupon_id'        => $coupon?->id,
                    'coupon_code'      => $coupon?->coupon_code, // FIX: lưu mã coupon vào snapshot
                    'receiver_name'    => $request->receiver_name,
                    'receiver_phone'   => $request->receiver_phone,
                    'shipping_address' => $shippingAddress,
                    'order_status'     => Order::STATUS_PENDING,
                    'subtotal'         => $subtotal,
                    'discount_amount'  => $discountAmount,
                    'total_amount'     => $totalAmount,
                    'note'             => $request->note,
                ]);

                // ── Bước 4: Tạo order items với đầy đủ snapshot ───────────
                foreach ($cart as $item) {
                    $variant = $variants[$item['id']];
                    $now = now();
                    $unitPrice = $this->getEffectivePrice($variant, $now);

                    // Mô tả biến thể: "Màu sắc: Đen - Kết nối: Bluetooth"
                    $variantDescription = $variant->attributeValues
                        ->sortBy('attribute_id')
                        ->map(fn($av) => $av->attribute->name . ': ' . $av->value)
                        ->implode(' - ');

                    if (!$variantDescription) {
                        $variantDescription = 'Mặc định';
                    }

                    // Ảnh: ưu tiên ảnh riêng của variant, fallback thumbnail sản phẩm
                    $thumbnail = $variant->image
                        ?? $variant->product->thumbnail
                        ?? null;

                    OrderItem::create([
                        'order_id'            => $order->id,
                        'variant_id'          => $variant->id,
                        'product_name'        => $variant->product->name,  // snapshot tên
                        'variant_description' => $variantDescription,       // snapshot phân loại
                        'variant_sku'         => $variant->sku,             // snapshot SKU
                        'product_thumbnail'   => $thumbnail,                // snapshot ảnh
                        'quantity'            => $item['qty'],
                        'unit_price'          => $unitPrice,                // snapshot giá bán
                        'compare_price'       => $variant->compare_price,   // snapshot giá gốc
                        'total_price'         => $unitPrice * $item['qty'],
                    ]);
                }

                // Xóa coupon khỏi session
                session()->forget(['coupon_code', 'coupon_id', 'discount_amount']);

                return $order;
            });

            // Gửi email xác nhận
            if ($order->user && $order->user->email) {
                Mail::to($order->user->email)->send(new OrderConfirmationMail($order));
            }

            return response()->json([
                'success'    => true,
                'message'    => 'Đặt hàng thành công!',
                'order_id'   => $order->id,
                'order_code' => 'NX-' . str_pad($order->id, 6, '0', STR_PAD_LEFT),
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
            ], 400);
        }
    }

    /**
     * Tính giá thực tế của variant:
     * - Nếu đang trong thời gian flash sale → dùng sale_price
     * - Không thì dùng price bình thường
     *
     * Giải thích: DB có sale_price, sale_starts_at, sale_ends_at.
     * Phải kiểm tra cả 3 điều kiện: có giá sale, đã bắt đầu, chưa kết thúc.
     */
    private function getEffectivePrice(ProductVariant $variant, \Carbon\Carbon $now): float
    {
        if (
            $variant->sale_price > 0
            && $variant->sale_starts_at
            && $variant->sale_ends_at
            && $now->between($variant->sale_starts_at, $variant->sale_ends_at)
        ) {
            return (float) $variant->sale_price;
        }

        return (float) $variant->price;
    }
}
