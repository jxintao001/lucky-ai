<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * App\Models\ProductSkuSub
 *
 * @property int $id
 * @property int|null $product_id 商品id
 * @property int|null $product_sku_id sku id
 * @property float $price 售价
 * @property float|null $tax_rate 税率
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property int $shop_id 店铺id
 * @property-read \App\Models\Product|null $product
 * @property-read \App\Models\ProductSku $productSku
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\ProductSkuSub newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\ProductSkuSub newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\ProductSkuSub query()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\ProductSkuSub whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\ProductSkuSub whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\ProductSkuSub wherePrice($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\ProductSkuSub whereProductId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\ProductSkuSub whereProductSkuId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\ProductSkuSub whereShopId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\ProductSkuSub whereTaxRate($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\ProductSkuSub whereUpdatedAt($value)
 * @mixin \Eloquent
 * @property float $tax_price 含税会员价
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\ProductSkuSub whereTaxPrice($value)
 */
class ProductSkuSub extends Model
{
    public function product()
    {
        return $this->belongsTo(Product::class,'product_id','id','shop_id', 'shop_id');
    }

    public function productSku()
    {
        return $this->belongsTo(ProductSku::class,'sku_id','id','shop_id', 'shop_id');
    }


}
