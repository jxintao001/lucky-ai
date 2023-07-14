<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;


class ProductSkuCard extends Model
{

    protected $guarded = ['id'];

    protected $dates = [
        'activated_at',
        'exchange_at',
        'used_at',
    ];


    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function sku()
    {
        return $this->belongsTo(ProductSku::class, 'product_sku_id');
    }

}
