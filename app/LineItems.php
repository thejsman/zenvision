<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class LineItems extends Model
{
    protected $guarded =[];

    protected $casts = [
        'properties' => 'array',
        'discount_allocations'=> 'array',
        'duties' => 'array',
        'tax_lines' => 'array'
    ]
}
