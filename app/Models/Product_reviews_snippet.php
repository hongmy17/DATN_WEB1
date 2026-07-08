<?php
// ============================================================
// THÊM VÀO CUỐI PHẦN RELATIONSHIPS trong app/Models/Product.php
// ============================================================

    public function reviews()
    {
        return $this->hasMany(\App\Models\Review::class);
    }

    // Điểm trung bình — dùng nhanh ở ngoài view nếu cần
    public function getAvgRatingAttribute(): float
    {
        return round($this->reviews()->visible()->avg('rating') ?? 0, 1);
    }

    public function getReviewCountAttribute(): int
    {
        return $this->reviews()->visible()->count();
    }
