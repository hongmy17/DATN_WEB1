{{-- resources/views/pages/product/_review_list.blade.php --}}

@forelse ($reviews as $review)
    <div class="review-item" id="review-{{ $review->id }}">
        <div class="review-item__header">
            <div class="review-item__avatar">
                @if ($review->user->avatar)
                    <img src="{{ asset('storage/' . $review->user->avatar) }}" alt="">
                @else
                    <div class="review-item__avatar-placeholder">
                        {{ mb_substr($review->user->name ?? 'U', 0, 1) }}
                    </div>
                @endif
            </div>

            <div class="review-item__meta">
                <div class="review-item__name">{{ $review->user->name ?? 'Người dùng' }}</div>
                <div class="review-item__stars">
                    @for ($i = 1; $i <= 5; $i++)
                        <svg width="13" height="13" viewBox="0 0 24 24"
                            fill="{{ $i <= $review->rating ? '#F59E0B' : '#E5E3DE' }}"
                            stroke="{{ $i <= $review->rating ? '#F59E0B' : '#E5E3DE' }}" stroke-width="1"></svg>
                            <polygon
                                points="12 2 15.09 8.26 22 9.27 17 14.14 18.18 21.02 12 17.77 5.82 21.02 7 14.14 2 9.27 8.91 8.26 12 2" />
                        </svg>
                    @endfor
                    <span class="review-item__date">{{ $review->created_at->format('d/m/Y H:i') }}</span>
                </div>
                @if ($review->orderItem)
                    <div class="review-item__verified">
                        <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="#16a34a"
                            stroke-width="2.5" stroke-linecap="round">
                            <polyline points="20 6 9 17 4 12" />
                        </svg>
                        Đã mua hàng · {{ $review->orderItem->variant_description ?? '' }}
                    </div>
                @endif
            </div>

            {{-- Nút sửa --}}
            @auth
                @if (auth()->id() === $review->user_id && $review->canEdit())
                    <button class="review-item__edit-btn" onclick="toggleEditForm({{ $review->id }})"
                        title="Sửa đánh giá">
                        <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                            stroke-width="2" stroke-linecap="round">
                            <path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7" />
                            <path d="M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5z" />
                        </svg>
                        Sửa
                    </button>
                @endif
            @endauth
        </div>

        {{-- Nội dung --}}
        <div class="review-item__comment" id="comment-{{ $review->id }}">
            {{ $review->comment }}
            @if ($review->edit_count > 0 && $review->edited_at)
                <span class="review-item__edited">· Đã chỉnh sửa {{ $review->edited_at->format('d/m/Y H:i') }}</span>
            @endif
        </div>

        {{-- Form sửa inline --}}
        @auth
            @if (auth()->id() === $review->user_id && $review->canEdit())
                <div class="review-item__edit-form" id="edit-form-{{ $review->id }}" style="display:none">
                    <form method="POST" action="{{ route('products.reviews.update', [$product->slug, $review->id]) }}">
                        @csrf
                        @method('PUT')

                        <div class="edit-star-picker" id="edit-stars-{{ $review->id }}">
                            @for ($i = 1; $i <= 5; $i++)
                                <svg width="24" height="24" viewBox="0 0 24 24"
                                    fill="{{ $i <= $review->rating ? '#F59E0B' : '#E5E3DE' }}"
                                    stroke="{{ $i <= $review->rating ? '#F59E0B' : '#E5E3DE' }}" stroke-width="1"
                                    style="cursor:pointer"
                                    onclick="selectEditStar({{ $review->id }}, {{ $i }})"
                                    data-star="{{ $i }}">
                                    <polygon
                                        points="12 2 15.09 8.26 22 9.27 17 14.14 18.18 21.02 12 17.77 5.82 21.02 7 14.14 2 9.27 8.91 8.26 12 2" />
                                </svg>
                            @endfor
                        </div>
                        <input type="hidden" name="rating" id="edit-rating-{{ $review->id }}"
                            value="{{ $review->rating }}">

                        <textarea name="comment" class="review-item__edit-textarea" rows="3">{{ $review->comment }}</textarea>

                        <div class="review-item__edit-actions">
                            <button type="submit" class="btn-edit-save">Lưu</button>
                            <button type="button" class="btn-edit-cancel"
                                onclick="toggleEditForm({{ $review->id }})">Huỷ</button>
                        </div>
                    </form>
                </div>
            @endif
        @endauth

        {{-- Ảnh review --}}
        @if (!empty($review->images))
            <div class="review-item__images">
                @foreach ($review->images as $img)
                    <a href="{{ asset('storage/' . $img) }}" target="_blank">
                        <img src="{{ asset('storage/' . $img) }}" alt="Ảnh đánh giá">
                    </a>
                @endforeach
            </div>
        @endif

        {{-- Phản hồi của Shop + reply của khách --}}
        @foreach ($review->replies as $reply)
            <div class="review-item__reply">
                <div class="review-item__reply-label">
                    <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                        stroke-width="2" stroke-linecap="round">
                        <polyline points="9 17 4 12 9 7" />
                        <path d="M20 18v-2a4 4 0 0 0-4-4H4" />
                    </svg>
                    Phản hồi của Shop · {{ $reply->created_at->format('d/m/Y H:i') }}
                </div>
                <div class="review-item__reply-text">{{ $reply->comment }}</div>

                {{-- Reply của khách vào admin reply --}}
                @foreach ($reply->childReplies as $child)
                    <div class="review-item__child-reply">
                        <div class="review-item__child-reply-name">
                            {{ $child->user->name ?? 'Người dùng' }}
                            <span class="review-item__child-reply-date">·
                                {{ $child->created_at->format('d/m/Y H:i') }}</span>
                        </div>
                        <div class="review-item__child-reply-text">{{ $child->comment }}</div>
                    </div>
                @endforeach

                {{-- Form khách reply lại admin --}}
                @auth
                    <div class="review-item__reply-form-wrap">
                        <button class="review-item__reply-toggle"
                            onclick="toggleReplyForm('reply-form-{{ $reply->id }}')">
                            <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                                stroke-width="2" stroke-linecap="round">
                                <polyline points="9 17 4 12 9 7" />
                                <path d="M20 18v-2a4 4 0 0 0-4-4H4" />
                            </svg>
                            Phản hồi
                        </button>
                        <div class="review-item__reply-form" id="reply-form-{{ $reply->id }}" style="display:none">
                            <form method="POST"
                                action="{{ route('products.reviews.reply', [$product->slug, $reply->id]) }}">
                                @csrf
                                <textarea name="comment" class="review-item__edit-textarea" rows="2" placeholder="Viết phản hồi của bạn..."></textarea>
                                <div class="review-item__edit-actions">
                                    <button type="submit" class="btn-edit-save">Gửi</button>
                                    <button type="button" class="btn-edit-cancel"
                                        onclick="toggleReplyForm('reply-form-{{ $reply->id }}')">Huỷ</button>
                                </div>
                            </form>
                        </div>
                    </div>
                @endauth
            </div>
        @endforeach
    </div>
@empty
    <p class="review-empty">Chưa có đánh giá nào{{ request('rating') ? ' ở mức ' . request('rating') . ' sao' : '' }}.
    </p>
@endforelse

<script>
    function toggleEditForm(id) {
        const form = document.getElementById('edit-form-' + id);
        const comment = document.getElementById('comment-' + id);
        const isHidden = form.style.display === 'none';
        form.style.display = isHidden ? 'block' : 'none';
        comment.style.display = isHidden ? 'none' : 'block';
    }

    function selectEditStar(reviewId, n) {
        document.getElementById('edit-rating-' + reviewId).value = n;
        document.querySelectorAll('#edit-stars-' + reviewId + ' svg').forEach((s, i) => {
            const on = i < n;
            s.setAttribute('fill', on ? '#F59E0B' : '#E5E3DE');
            s.setAttribute('stroke', on ? '#F59E0B' : '#E5E3DE');
        });
    }

    function toggleReplyForm(id) {
        const form = document.getElementById(id);
        form.style.display = form.style.display === 'none' ? 'block' : 'none';
    }
</script>
