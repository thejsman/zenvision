<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class ShopifyOrder extends Model
{
    protected $guarded = [];

    // for JSON data types
    protected $casts = [
        'discount_applications' => 'array',
        'tax_lines' => 'array',
        'refunds' => 'array',
        'shipping_lines' => 'array'
    ];

    public function getCogs()
    {
        return $this->hasMany(ShopifyOrderProduct::class, 'order_id', 'order_id')->get()->sum('total_cost');
    }
}
