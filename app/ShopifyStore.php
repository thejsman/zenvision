<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use App\ShopifyOrderProduct;
use App\ShopifyOrder;

class ShopifyStore extends Model
{
    protected $fillable = [
        'user_id', 'store_name', 'store_url', 'api_token'
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function getOrderedProductCount()
    {
        return $this->hasMany(ShopifyOrderProduct::class, 'store_id')->get()->count();
    }

    public function getOrders()
    {
        $orders =  $this->hasMany(ShopifyOrder::class, 'store_id')->where('is_deleted', false)->whereIn('financial_status', ['paid', 'pending', 'partially_paid'])->get();

        foreach ($orders as $order) {
            $order['total_cost'] = $order->getCogs();
        }
        return $orders;
    }
    public function getStoreDetails()
    {
        return [
            'id' => $this->id,
            'user_id' => $this->user_id,
            'store_name' => $this->store_name,
            'store_url' => $this->store_url,
            'api_token' => $this->api_token
        ];
    }

    public function getRefundTotal()
    {
        return  $this->hasMany(ShopifyOrder::class, 'store_id')->where('is_deleted', false)->whereIn('financial_status', ['refunded', 'voided'])->get()->sum('total_price');
    }
}
