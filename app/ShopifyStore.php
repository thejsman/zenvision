<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class ShopifyStore extends Model
{
    protected $fillable = [
        'user_id', 'store_name' , 'store_url','api_token'
    ];

    public function user() {
        return $this->belongsTo(User::class);
    }
}
