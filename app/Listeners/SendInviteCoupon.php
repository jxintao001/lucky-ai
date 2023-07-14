<?php

namespace App\Listeners;

use App\Events\UserRegistered;
use App\Models\CouponCode;
use App\Models\InviteItem;
use App\Models\UserCouponCode;
use App\Services\CouponService;
use App\User;
use Carbon\Carbon;
use Dingo\Api\Exception\ResourceException;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Support\Facades\DB;

//  implements ShouldQueue 代表此监听器是异步执行的
class SendInviteCoupon implements ShouldQueue
{
    // Laravel 会默认执行监听器的 handle 方法，触发的事件会作为 handle 方法的参数
    public function handle(UserRegistered $event)
    {
        // 被邀请者
        $user = $event->getUser();
        // 邀请者
        $inviter = User::find($event->getInviterId());
        if (!$user || !$inviter){
            return false;
        }
        // 查询邀请券发放记录
        $invite_item = InviteItem::where('user_id',$inviter->id)
            ->where('invite_user_id',$user->id)
            ->first();
        if ($invite_item){
            return false;
        }
        // 获取邀请券
        $coupon = CouponCode::where('enabled', 1)
            ->where('use_type', CouponCode::USE_TYPE_INVITE)
            ->where('shop_id', $inviter->shop_id)
            ->first();
        if (!$coupon){
            return false;
        }
        // 领取检查
        if (!$coupon->enabled) {
            return false;
        }

        if ($coupon->shop_id != $inviter->shop_id) {
            return false;
        }

        if ($coupon->total - $coupon->received <= 0) {
            return false;
        }

        if ($coupon->not_after && $coupon->not_after->lt(Carbon::now())) {
            return false;
        }

        $count = UserCouponCode::where('user_id', $inviter->id)
            ->where('coupon_code_id', $coupon->id)
            ->count();
        if ($count >= $coupon->limit_receive) {
            return false;
        }

        DB::transaction(function () use ($inviter, $coupon, $user) {
            // 创建一个用户优惠券
            $user_coupon = new UserCouponCode([
                'used'    => false
            ]);
            // 优惠券关联到当前用户
            $user_coupon->user()->associate($inviter);
            $user_coupon->couponCode()->associate($coupon);
            $user_coupon->shop_id = $coupon->shop_id;
            // 写入数据库
            $user_coupon->save();
            // 优惠券发放记录
            $invite_item = new InviteItem([
                'user_id'           => $inviter->id,
                'invite_user_id'    => $user->id,
                'coupon_id'         => $coupon->id,
                'shop_id'           => $coupon->shop_id
            ]);
            $invite_item->save();
            // 增加优惠券的领取量，需判断返回值
            if ($coupon->changeReceived() <= 0) {
                return false;
            }
        });

    }
}
