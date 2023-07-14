<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;

/**
 * App\Models\CombinationProduct
 *
 * @property int $id id
 * @property int $product_id 商品id
 * @property int $sku_id 商品sku_id
 * @property float $price 售价
 * @property int $amount 数量
 * @property float|null $tax_rate 税率
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property int $shop_id
 * @property-read \App\Models\Product $product
 * @property-read \App\Models\ProductSku $productSku
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\CombinationProduct newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\CombinationProduct newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\CombinationProduct query()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\CombinationProduct whereAmount($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\CombinationProduct whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\CombinationProduct whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\CombinationProduct wherePrice($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\CombinationProduct whereProductId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\CombinationProduct whereShopId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\CombinationProduct whereSkuId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\CombinationProduct whereTaxRate($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\CombinationProduct whereUpdatedAt($value)
 * @mixin \Eloquent
 * @property float|null $tax_price 普通会员价
 * @property float|null $club_tax_price CLUB会员价
 * @property float|null $club_price
 * @property float|null $club_tax_rate
 * @property-read \App\Models\CombinationProductSub|null $combinationProductSub
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\CombinationProduct whereClubPrice($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\CombinationProduct whereClubTaxPrice($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\CombinationProduct whereClubTaxRate($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\CombinationProduct whereTaxPrice($value)
 */
class CombinationProduct extends Model
{
    protected $guarded = ['id'];

    /**
     *模型的「启动」方法.
     *
     * @return void
     */
    protected static function boot()
    {
        parent::boot();

        static::addGlobalScope('combinationProductSub', function (Builder $builder) {
            $builder->with('combinationProductSub');
        });
    }

    public function combinationProductSub()
    {
        $shop_id = intval(request('shop_id', 0));
        return $this->hasOne(CombinationProductSub::class)
            ->where(['combination_product_subs.shop_id' => $shop_id]);
    }

    public function product()
    {
        return $this->belongsTo(Product::class,'product_id','id','shop_id', 'shop_id');
    }

    public function productSku()
    {
        return $this->belongsTo(ProductSku::class,'sku_id','id','shop_id', 'shop_id');
    }

    public function getTaxRateAttribute($value)
    {
        $shop_id = intval(request('shop_id', 0));
        $shop = Shop::find($shop_id);
        if ($shop && $shop->fixed_rate == 1 && $value != 0){
            return 9.1;
        }else{
            return $this->combinationProductSub !== null ? $this->combinationProductSub->tax_rate : $value;
        }

    }

    public function getPriceAttribute($value)
    {
        return $this->combinationProductSub !== null ? $this->combinationProductSub->price : $value;
    }



}
