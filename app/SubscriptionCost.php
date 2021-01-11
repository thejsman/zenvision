<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class SubscriptionCost extends Model
{
    protected $fillable = [
        'user_id', 'subscription_name', 'billing_period', 'subscription_price', 'starting_date', 'end_date'
    ];
}
