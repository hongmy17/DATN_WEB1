<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreReviewRequest;
use App\Models\OrderItem;
use App\Models\Product;
use App\Models\Review;
use App\Models\ReviewReply;
use Illuminate\Http\Request;

class ReviewController extends Controller
{
    /**
     * Gửi đánh giá mới.
     */
    public function store(StoreReviewRequest $request, string $slug)
    {
        $product = Product::where('slug', $slug)->firstOrFail();
        $user    = auth()->user();

        $orderItem = OrderItem::whereHas('order', function ($q) use ($user) {
                $q->where('user_id', $user->id)
                  ->where('order_status', 3);
            })
            ->where('id', $request->order_item_id)
            ->whereHas('variant', fn ($q) => $q->where('product_id', $product->id))
            ->firstOrFail();

        if (Review::hasReviewed($orderItem->id)) {
            return back()->with('review_error', 'Bạn đã đánh giá sản phẩm này rồi.');
        }

        $imagePaths = [];
        if ($request->hasFile('images')) {
            foreach ($request->file('images') as $file) {
                $imagePaths[] = $file->store('reviews', 'public');
            }
        }

        Review::create([
            'product_id'    => $product->id,
            'user_id'       => $user->id,
            'order_item_id' => $orderItem->id,
            'rating'        => $request->rating,
            'comment'       => $request->comment,
            'images'        => $imagePaths ?: null,
            'status'        => true,
            'edit_count'    => 0,
        ]);

        return back()->with('review_success', 'Đánh giá của bạn đã được ghi nhận. Cảm ơn bạn!');
    }

    /**
     * Sửa đánh giá — chỉ 1 lần, lưu thêm edited_at.
     */
    public function update(Request $request, string $slug, Review $review)
    {
        $user = auth()->user();

        if ($review->user_id !== $user->id) {
            return back()->with('review_error', 'Bạn không có quyền sửa đánh giá này.');
        }

        if ($review->edit_count >= 1) {
            return back()->with('review_error', 'Bạn chỉ được sửa đánh giá 1 lần.');
        }

        $request->validate([
            'rating'  => ['required', 'integer', 'min:1', 'max:5'],
            'comment' => ['nullable', 'string', 'max:2000'],
        ]);

        $review->update([
            'rating'     => $request->rating,
            'comment'    => $request->comment,
            'edit_count' => 1,
            'edited_at'  => now(),
        ]);

        return back()->with('review_success', 'Đã cập nhật đánh giá của bạn.');
    }

    /**
     * Khách reply lại phản hồi của admin.
     */
    public function reply(Request $request, string $slug, ReviewReply $reply)
    {
        // Chỉ reply vào admin reply (parent_id = null), không cho reply lồng sâu hơn
        if ($reply->parent_id !== null) {
            return back()->with('review_error', 'Không thể reply vào phản hồi này.');
        }

        $request->validate([
            'comment' => ['required', 'string', 'max:1000'],
        ]);

        ReviewReply::create([
            'review_id' => $reply->review_id,
            'parent_id' => $reply->id,
            'user_id'   => auth()->id(),
            'comment'   => $request->comment,
        ]);

        return back()->with('review_success', 'Đã gửi phản hồi của bạn.');
    }

    /**
     * Load thêm reviews (AJAX).
     */
    public function load(Request $request, string $slug)
    {
        $product = Product::where('slug', $slug)->firstOrFail();

        $query = $product->reviews()
            ->visible()
            ->with(['user', 'replies.user', 'replies.childReplies.user', 'orderItem'])
            ->latest();

        if ($request->filled('rating')) {
            $query->where('rating', (int) $request->rating);
        }

        if ($request->boolean('has_image')) {
            $query->whereNotNull('images')->where('images', '!=', '[]');
        }

        $reviews = $query->paginate(5, ['*'], 'page', $request->page ?? 1);

        return response()->json([
            'html'     => view('pages.product._review_list', [
                'reviews' => $reviews,
                'product' => $product,
            ])->render(),
            'has_more' => $reviews->hasMorePages(),
        ]);
    }
}
