<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class UserIntegral extends Model
{
    use SoftDeletes;

    const TYPE_GET = 'get';
    const TYPE_USE = 'use';

    const GET_METHOD_ORDER_EXCHANGE = 'order_exchange';
    const GET_METHOD_ORDER_RETURN = 'order_return';

    const USE_METHOD_ORDER_DEDUCTION = 'order_deduction';

    public static $typeMap = [
        self::TYPE_GET => '获取',
        self::TYPE_USE => '使用',
    ];

    public static $getMethodMap = [
        self::GET_METHOD_ORDER_EXCHANGE => '订单兑换',
        self::GET_METHOD_ORDER_RETURN   => '订单退还',
    ];

    public static $useMethodMap = [
        self::USE_METHOD_ORDER_DEDUCTION => '订单抵扣',
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

    public function productSku()
    {
        return $this->belongsTo(ProductSku::class, 'sku_id')->withTrashed();
    }

    public function order()
    {
        return $this->belongsTo(Order::class);
    }

}
