<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * App\Models\ProductSub
 *
 * @property int $id
 * @property int|null $product_id 商品id
 * @property float $original_price 原价
 * @property float $price 售价
 * @property int $on_sale 是否上架
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property int $shop_id 店铺id
 * @property-read \App\Models\Product|null $product
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\ProductSub newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\ProductSub newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\ProductSub query()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\ProductSub whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\ProductSub whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\ProductSub whereOnSale($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\ProductSub whereOriginalPrice($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\ProductSub wherePrice($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\ProductSub whereProductId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\ProductSub whereShopId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\ProductSub whereUpdatedAt($value)
 * @mixin \Eloquent
 */
class ProductSub extends Model
{
    public function product()
    {
        return $this->belongsTo(Product::class,'product_id','id','shop_id', 'shop_id');
    }


}
