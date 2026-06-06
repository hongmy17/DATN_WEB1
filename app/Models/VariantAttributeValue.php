<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class VariantAttributeValue extends Model
{
    protected $fillable = ['variant_id', 'attribute_value_id'];

     public $timestamps = false;
}
