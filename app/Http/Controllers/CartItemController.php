<?php

namespace App\Http\Controllers;

use App\Models\CartItem;
use App\Models\ProductVariant;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CartItemController extends Controller
{
    /** GET /api/cart — lấy toàn bộ giỏ hàng */
    public function index()
    {
        $items = CartItem::with(['variant.product', 'variant.attributeValues.attribute'])
            ->where('user_id', Auth::id())
            ->get()
            ->map(fn($item) => $this->format($item));

        return response()->json(['success' => true, 'data' => $items]);
    }

    /** POST /api/cart — thêm sản phẩm { variant_id, quantity? } */
    public function store(Request $request)
    {
        $request->validate([
            'variant_id' => 'required|integer|exists:product_variants,id',
            'quantity'   => 'sometimes|integer|min:1|max:99',
        ]);

        $variant = ProductVariant::findOrFail($request->variant_id);

        if (!$variant->status) {
            return response()->json(['success' => false, 'message' => 'Sản phẩm không còn bán.'], 422);
        }

        $qty = (int) $request->input('quantity', 1);

        $item = CartItem::where('user_id', Auth::id())
            ->where('variant_id', $variant->id)
            ->first();

        if ($item) {
            $item->update(['quantity' => min($item->quantity + $qty, 99)]);
        } else {
            $item = CartItem::create([
                'user_id'    => Auth::id(),
                'variant_id' => $variant->id,
                'quantity'   => $qty,
            ]);
        }

        $item->load(['variant.product', 'variant.attributeValues.attribute']);

        return response()->json(['success' => true, 'data' => $this->format($item)], 201);
    }

    /** PATCH /api/cart/{cartItem} — cập nhật số lượng { quantity } */
    public function update(Request $request, CartItem $cartItem)
    {
        abort_if($cartItem->user_id !== Auth::id(), 403);
        $request->validate(['quantity' => 'required|integer|min:1|max:99']);
        $cartItem->update(['quantity' => (int) $request->quantity]);
        return response()->json(['success' => true]);
    }

    /** DELETE /api/cart/{cartItem} — xoá 1 item */
    public function destroy(CartItem $cartItem)
    {
        abort_if($cartItem->user_id !== Auth::id(), 403);
        $cartItem->delete();
        return response()->json(['success' => true]);
    }

    /** DELETE /api/cart — xoá toàn bộ */
    public function clear()
    {
        CartItem::where('user_id', Auth::id())->delete();
        return response()->json(['success' => true]);
    }

    /** POST /api/cart/sync — đồng bộ localStorage lên DB sau khi đăng nhập */
    public function sync(Request $request)
    {
        $request->validate([
            'items'              => 'required|array',
            'items.*.variant_id' => 'required|integer|exists:product_variants,id',
            'items.*.quantity'   => 'required|integer|min:1|max:99',
        ]);

        foreach ($request->items as $row) {
            CartItem::updateOrCreate(
                ['user_id' => Auth::id(), 'variant_id' => $row['variant_id']],
                ['quantity' => min((int) $row['quantity'], 99)]
            );
        }

        // Trả về giỏ hàng đã merge để JS cập nhật localStorage
        $items = CartItem::with(['variant.product', 'variant.attributeValues.attribute'])
            ->where('user_id', Auth::id())
            ->get()
            ->map(fn($item) => $this->format($item));

        return response()->json(['success' => true, 'data' => $items]);
    }

    /** Format item trả về cho JS — khớp đúng cấu trúc Cart.add() */
    private function format(CartItem $item): array
    {
        $variant = $item->variant;
        $product = $variant?->product;
        $variantLabel = $variant?->attributeValues
            ->map(fn($v) => $v->value)
            ->filter()
            ->implode(' / ');

        return [
            'cart_item_id' => $item->id,
            'variant_id'   => $item->variant_id,
            'id'           => $product?->id,
            'name'         => $product?->name ?? 'Sản phẩm',
            'variant'      => $variantLabel ?: '',
            'price'        => (int) ($variant?->price ?? 0),
            'qty'          => $item->quantity,
            'img'          => $variant?->display_image ?? '',
        ];
    }
}