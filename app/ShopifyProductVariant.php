<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class ShopifyProductVariant extends Model
{
    protected $guarded =[];

   public function isAllCogsUpdated()
    {
     return  $this->whereNull('cost')->get()->count() + $this->whereNull('shipping_cost')->get()->count();

    }
}
