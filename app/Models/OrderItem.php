<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * App\Models\OrderItem
 *
 * @property int $id
 * @property int $order_id 订单id
 * @property int $product_id 商品id
 * @property int $product_sku_id sku_id
 * @property int $cp_id 组合装商品ID
 * @property int $amount 数量
 * @property float $price 售价
 * @property float $coupon 优惠金额
 * @property float $coupon_amount 优惠金额汇总
 * @property float $tax_rate 税率
 * @property float $tax 税额
 * @property float $cost 成本价
 * @property float $profit 利润
 * @property int|null $rating
 * @property string|null $review
 * @property \Illuminate\Support\Carbon|null $reviewed_at
 * @property int $shop_id
 * @property-read \App\Models\BargainProduct $bargainProduct
 * @property-read \App\Models\GroupProduct $groupProduct
 * @property-read \App\Models\Order $order
 * @property-read \App\Models\Product $product
 * @property-read \App\Models\ProductSku $productSku
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\OrderItem newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\OrderItem newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\OrderItem query()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\OrderItem whereAmount($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\OrderItem whereCost($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\OrderItem whereCoupon($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\OrderItem whereCouponAmount($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\OrderItem whereCpId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\OrderItem whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\OrderItem whereOrderId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\OrderItem wherePrice($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\OrderItem whereProductId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\OrderItem whereProductSkuId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\OrderItem whereProfit($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\OrderItem whereRating($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\OrderItem whereReview($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\OrderItem whereReviewedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\OrderItem whereShopId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\OrderItem whereTax($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\OrderItem whereTaxRate($value)
 * @mixin \Eloquent
 * @property int|null $qty SKU数量
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\OrderItem whereQty($value)
 */
class OrderItem extends Model
{
    protected $guarded = ['id'];
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
        return $this->belongsTo(Product::class)->withTrashed();
    }

    public function productSku()
    {
        return $this->belongsTo(ProductSku::class)->withTrashed();
    }

    public function bargainProduct()
    {
        return $this->belongsTo(BargainProduct::class,'product_id','product_id')->withDefault();
    }

    public function groupProduct()
    {
        return $this->belongsTo(GroupProduct::class, 'product_id','product_id')->withDefault();
    }

    public function order()
    {
        return $this->belongsTo(Order::class);
    }
}
