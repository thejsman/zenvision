<?php

namespace App;
use Auth;
use Illuminate\Database\Eloquent\Model;

class ShopifyOrderProduct extends Model
{
    protected $guarded =[];

    protected $casts = [
        'duties' => 'array',
        'tax_lines' => 'array'
    ];
}
