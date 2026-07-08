<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ReviewReply extends Model
{
    protected $fillable = ['review_id', 'parent_id', 'user_id', 'comment'];

    public function review()
    {
        return $this->belongsTo(Review::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function parent()
    {
        return $this->belongsTo(ReviewReply::class, 'parent_id');
    }

    // Reply của khách (con của admin reply)
    public function childReplies()
    {
        return $this->hasMany(ReviewReply::class, 'parent_id')
            ->with('user')
            ->orderBy('created_at');
    }
}
