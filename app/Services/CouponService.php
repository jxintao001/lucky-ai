<?php

namespace App\Services;

use App\Exceptions\CouponCodeUnavailableException;
use App\Exceptions\InternalException;
use App\Jobs\CloseBargain;
use App\Jobs\RefundInstallmentOrder;
use App\Models\Bargain;
use App\Models\BargainItem;
use App\Models\Group;
use App\Models\GroupItem;
use App\Models\User;
use App\Models\UserAddress;
use App\Models\Order;
use App\Models\ProductSku;
use App\Models\CouponCode;
use App\Exceptions\InvalidRequestException;
use App\Jobs\CloseOrder;
use App\Models\UserCouponCode;
use Carbon\Carbon;
use Dingo\Api\Exception\ResourceException;

class CouponService
{
    public function receive(User $user, CouponCode $coupon)
    {
        $coupon->checkReceive($user);
        // 开启事务
        $user_coupon = \DB::transaction(function () use ($user, $coupon) {
            // 创建一个用户优惠券
            $user_coupon = new UserCouponCode([
                'used'    => false
            ]);
            // 优惠券关联到当前用户
            $user_coupon->user()->associate($user);
            $user_coupon->couponCode()->associate($coupon);
            $user_coupon->shop_id = $coupon->shop_id;
            // 写入数据库
            $user_coupon->save();
            // 增加优惠券的领取量，需判断返回值
            if ($coupon->changeReceived() <= 0) {
                throw new ResourceException('该优惠券已被领完');
            }
            return $user_coupon;
        });

        return $user_coupon;
    }


}
