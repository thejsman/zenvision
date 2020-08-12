<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Shopify_store extends Model
{
    protected $fillable = [
        'store_name', 'store_url' , 'api_token','status'
    ];
}
