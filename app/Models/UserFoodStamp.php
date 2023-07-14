<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class UserFoodStamp extends Model
{
    use SoftDeletes;

    const TYPE_GET = 'get';
    const TYPE_USE = 'use';

    // 获得
    const ACTION_TYPE_RECHARGE = 'recharge';
    const ACTION_TYPE_ORDER_EXCHANGE = 'order_exchange';
    const ACTION_TYPE_ORDER_RETURN = 'order_return';

    // 使用
    const ACTION_TYPE_ORDER_PAY = 'order_pay';
    const ACTION_TYPE_GIFT_ORDER_PAY = 'gift_order_pay';

    const ACTION_TYPE_SECONDARY_ORDER_PAY = 'secondary_order_pay';
    const ACTION_TYPE_SECONDARY_GIFT_ORDER_PAY = 'secondary_gift_order_pay';

    public static $typeMap = [
        self::TYPE_GET => '获取',
        self::TYPE_USE => '使用',
    ];

    public static $actionTypeMap = [
        self::ACTION_TYPE_RECHARGE                 => '充值',
        self::ACTION_TYPE_ORDER_RETURN             => '订单退还',
        self::ACTION_TYPE_ORDER_EXCHANGE           => '订单兑换',
        self::ACTION_TYPE_ORDER_PAY                => '订单支付',
        self::ACTION_TYPE_GIFT_ORDER_PAY           => '礼物订单支付',
        self::ACTION_TYPE_SECONDARY_ORDER_PAY      => '副卡订单支付',
        self::ACTION_TYPE_SECONDARY_GIFT_ORDER_PAY => '副卡礼物订单支付',
    ];

    protected $guarded = ['id'];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function order()
    {
        return $this->belongsTo(Order::class);
    }

    public function shop()
    {
        return $this->belongsTo(Shop::class);
    }

    public function friend()
    {
        return $this->hasOne(UserFriend::class, 'friend_id', 'user_id')
            ->where('user_friends.user_id', auth('api')->id())->withDefault();
    }

}
