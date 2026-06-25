<?php

namespace App\Http\Controllers;

use App\Models\Coupon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
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

        // Load danh sách mã giảm giá còn hiệu lực
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
            // Nếu có address_id thì không cần nhập tay, ngược lại bắt buộc
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

        // Nếu chọn địa chỉ đã lưu → tự lấy thông tin từ DB
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

                $productIds = $cart->pluck('id')->toArray();

                $variants = ProductVariant::with(['product', 'attributeValues.attribute'])
                    ->whereIn('product_id', $productIds)
                    ->orderBy('id')
                    ->get()
                    ->unique('product_id')
                    ->keyBy('product_id');

                $subtotal = 0;

                foreach ($cart as $item) {
                    $variant = $variants[$item['id']] ?? null;

                    if (!$variant) {
                        throw new \Exception('Sản phẩm trong giỏ hàng không tồn tại hoặc chưa có biến thể mặc định.');
                    }

                    if ($variant->stock_quantity < $item['qty']) {
                        throw new \Exception('Sản phẩm ' . $variant->product->name . ' không đủ tồn kho.');
                    }

                    $unitPrice = $variant->discount_price > 0
                        ? $variant->discount_price
                        : $variant->price;

                    $subtotal += $unitPrice * $item['qty'];
                }

                $discountAmount = 0;
                $coupon = null;

                if ($request->coupon_code) {
                    $coupon = Coupon::where('coupon_code', $request->coupon_code)->first();

                    if ($coupon && $coupon->isValid($subtotal)) {
                        $discountAmount = $coupon->calcDiscount($subtotal);
                        $coupon->increment('used_count');
                    } else {
                        $coupon = null;
                    }
                }

                $totalAmount = max($subtotal - $discountAmount, 0);

                $shippingAddress = $request->address_detail . ', '
                    . $request->ward . ', '
                    . $request->district . ', '
                    . $request->province;

                $order = Order::create([
                    'user_id' => Auth::id(),
                    'address_id' => $request->address_id,
                    'coupon_id' => $coupon?->id,
                    'receiver_name' => $request->receiver_name,
                    'receiver_phone' => $request->receiver_phone,
                    'shipping_address' => $shippingAddress,
                    'order_status' => Order::STATUS_PENDING,
                    'subtotal' => $subtotal,
                    'discount_amount' => $discountAmount,
                    'total_amount' => $totalAmount,
                    'note' => $request->note,
                ]);

                foreach ($cart as $item) {
                    $variant = $variants[$item['id']];
                    $variantDescription = $variant->attributeValues
                        ->map(function ($value) {
                            return $value->attribute->name . ': ' . $value->value;
                        })
                        ->implode(' - ');

                    if (!$variantDescription) {
                        $variantDescription = 'Mặc định';
                    }

                    $unitPrice = $variant->discount_price > 0
                        ? $variant->discount_price
                        : $variant->price;

                    OrderItem::create([
                        'order_id' => $order->id,
                        'variant_id' => $variant->id,

                        // Snapshot sản phẩm
                        'product_name' => $variant->product->name,
                        'variant_description' => $variantDescription,
                        'quantity' => $item['qty'],
                        'unit_price' => $unitPrice,
                        'total_price' => $unitPrice * $item['qty'],
                    ]);

                    $variant->decrement('stock_quantity', $item['qty']);
                }

                return $order;
            });

            return response()->json([
                'success' => true,
                'message' => 'Đặt hàng thành công.',
                'order_id' => $order->id,
                'order_code' => 'NX-' . str_pad($order->id, 6, '0', STR_PAD_LEFT),
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
            ], 400);
        }
    }
}
