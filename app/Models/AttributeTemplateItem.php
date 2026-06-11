<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AttributeTemplateItem extends Model
{
    protected $fillable = ['attribute_template_id', 'name', 'sort_order'];

    protected static function booted(): void
    {
        static::creating(function ($item) {
            if (empty($item->sort_order)) {
                $item->sort_order = static::where('attribute_template_id', $item->attribute_template_id)
                    ->max('sort_order') + 1;
            }
        });
    }

    public function template()
    {
        return $this->belongsTo(AttributeTemplate::class, 'attribute_template_id');
    }
}