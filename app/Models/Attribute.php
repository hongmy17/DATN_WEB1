<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Attribute extends Model
{
    protected $fillable = ['name', 'display_type'];

    public function attributeValues()
    {
        return $this->hasMany(AttributeValue::class);
    }
}
