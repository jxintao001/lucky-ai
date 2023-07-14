<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;


/**
 * App\Models\UserGiftItem
 *
 * @property int $id
 * @property string $type 类型（获得-get，使用-use）
 * @property int $user_id 用户ID
 * @property int|null $order_id 订单ID
 * @property int|null $gift_package_id 礼品包ID
 * @property int|null $product_id 商品ID
 * @property int|null $product_sku_id sku ID
 * @property string $sku_title sku 标题
 * @property string $sku_image sku 图片
 * @property float $sku_price sku 售价
 * @property int|null $amount 礼品数量
 * @property float|null $price 价格
 * @property string|null $get_method 获得方式（订单购买-order 礼包领取-gift_package 礼包退还-gift_package_return）
 * @property string|null $use_method 使用方式（发礼包-gift_package 赠送-give_away 兑换-exchange）
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property string|null $deleted_at
 * @property int $shop_id
 * @property-read \App\Models\Order|null $order
 * @property-read \App\Models\Product|null $product
 * @property-read \App\Models\ProductSku|null $productSku
 * @property-read \App\Models\User $user
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\UserGiftItem newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\UserGiftItem newQuery()
 * @method static \Illuminate\Database\Query\Builder|\App\Models\UserGiftItem onlyTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\UserGiftItem query()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\UserGiftItem whereAmount($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\UserGiftItem whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\UserGiftItem whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\UserGiftItem whereGetMethod($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\UserGiftItem whereGiftPackageId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\UserGiftItem whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\UserGiftItem whereOrderId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\UserGiftItem wherePrice($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\UserGiftItem whereProductId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\UserGiftItem whereProductSkuId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\UserGiftItem whereShopId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\UserGiftItem whereSkuImage($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\UserGiftItem whereSkuPrice($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\UserGiftItem whereSkuTitle($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\UserGiftItem whereType($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\UserGiftItem whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\UserGiftItem whereUseMethod($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\UserGiftItem whereUserId($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\UserGiftItem withTrashed()
 * @method static \Illuminate\Database\Query\Builder|\App\Models\UserGiftItem withoutTrashed()
 * @mixin \Eloquent
 */
class UserGiftItem extends Model
{
    use SoftDeletes;

    const TYPE_GET = 'get';
    const TYPE_USE = 'use';

    const GET_METHOD_ORDER = 'order';
    const GET_METHOD_GIFT_PACKAGE = 'gift_package';
    const GET_METHOD_GIFT_PACKAGE_RETURN = 'gift_package_return';

    const USE_METHOD_GIFT_PACKAGE = 'gift_package';
    const USE_METHOD_GIVE_AWAY = 'give_away';
    const USE_METHOD_EXCHANGE = 'exchange';

    public static $typeMap = [
        self::TYPE_GET => '获取',
        self::TYPE_USE => '使用',
    ];

    public static $getMethodMap = [
        self::GET_METHOD_ORDER               => '订单购买',
        self::GET_METHOD_GIFT_PACKAGE        => '礼包领取',
        self::GET_METHOD_GIFT_PACKAGE_RETURN => '礼包退还',
    ];

    public static $useMethodMap = [
        self::USE_METHOD_GIFT_PACKAGE => '发礼包',
        self::USE_METHOD_GIVE_AWAY    => '赠送',
        self::USE_METHOD_EXCHANGE     => '兑换',
    ];

    protected $guarded = ['id'];

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

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function product()
    {
        return $this->belongsTo(Product::class)->withTrashed();
    }

    public function productSku()
    {
        return $this->belongsTo(ProductSku::class)->withTrashed();
    }

    public function order()
    {
        return $this->belongsTo(Order::class);
    }

}
