<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class StripeAccount extends Model
{
    protected $guarded = [];
    protected $table = "stripe";
}
