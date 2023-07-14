<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * App\Models\OrderCpItem
 *
 * @property int $id
 * @property int $order_id 订单id
 * @property int $product_id 商品id
 * @property int $product_sku_id sku_id
 * @property int $amount 数量
 * @property float $price 售价
 * @property int $shop_id
 * @property-read \App\Models\Order $order
 * @property-read \App\Models\Product $product
 * @property-read \App\Models\ProductSku $productSku
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\OrderCpItem newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\OrderCpItem newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\OrderCpItem query()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\OrderCpItem whereAmount($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\OrderCpItem whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\OrderCpItem whereOrderId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\OrderCpItem wherePrice($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\OrderCpItem whereProductId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\OrderCpItem whereProductSkuId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\OrderCpItem whereShopId($value)
 * @mixin \Eloquent
 */
class OrderCpItem extends Model
{
    protected $fillable = ['amount', 'price', 'tax_rate', 'tax', 'rating', 'cost', 'coupon', 'coupon_amount', 'profit', 'review', 'reviewed_at', 'shop_id'];
    protected $dates = ['reviewed_at'];
    public $timestamps = false;

    protected static function boot()
    {
        parent::boot();
        static::creating(function ($model) {
            // 创建时自动填充店铺id
            if (!$model->shop_id) {
                if (auth('api')->user()->shop_id) {
                    $model->shop_id = auth('api')->user()->shop_id;
                } else {
                    return false;
                }
            }
        });
    }

    public function product()
    {
        return $this->belongsTo(Product::class);
    }

    public function productSku()
    {
        return $this->belongsTo(ProductSku::class);
    }

    public function order()
    {
        return $this->belongsTo(Order::class);
    }
}
