<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * App\Models\CombinationProductSub
 *
 * @property int $id
 * @property int $combination_product_id 组合id
 * @property int $product_id 商品id
 * @property int $product_sku_id sku id
 * @property float $tax_price 未含税会员价
 * @property float|null $price 售价
 * @property float|null $tax_rate 税率
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property int $shop_id 店铺id
 * @property-read \App\Models\Product $product
 * @property-read \App\Models\ProductSku $productSku
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\CombinationProductSub newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\CombinationProductSub newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\CombinationProductSub query()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\CombinationProductSub whereCombinationProductId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\CombinationProductSub whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\CombinationProductSub whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\CombinationProductSub wherePrice($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\CombinationProductSub whereProductId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\CombinationProductSub whereProductSkuId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\CombinationProductSub whereShopId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\CombinationProductSub whereTaxPrice($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\CombinationProductSub whereTaxRate($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\CombinationProductSub whereUpdatedAt($value)
 * @mixin \Eloquent
 */
class CombinationProductSub extends Model
{
    public function product()
    {
        return $this->belongsTo(Product::class, 'product_id', 'id', 'shop_id', 'shop_id');
    }

    public function productSku()
    {
        return $this->belongsTo(ProductSku::class, 'product_sku_id', 'id', 'shop_id', 'shop_id');
    }

}
