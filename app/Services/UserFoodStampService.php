<?php

namespace App\Services;

use App\Models\User;
use App\Models\UserFoodStamp;
use App\Models\UserSecondaryCard;
use Dingo\Api\Exception\ResourceException;

class UserFoodStampService
{

    public function getFoodStamp($action_type, $user, $food_stamp, $description = '', $order = null, $card_no = '')
    {
        if ($food_stamp <= 0) {
            throw new ResourceException('数量不能为0');
        }
        if (!in_array($action_type, array_flip(UserFoodStamp::$actionTypeMap))) {
            throw new ResourceException('操作类型错误');
        }
        $user = User::query()
            ->where('id', $user->id)
            ->lockForUpdate()
            ->first();
        if ($action_type === UserFoodStamp::ACTION_TYPE_RECHARGE) {
            $res = UserFoodStamp::where('type', UserFoodStamp::TYPE_GET)
                ->where('user_id', $user->id)
                ->where('order_id', $order->id)
                ->where('action_type', $action_type)
                ->lockForUpdate()
                ->first();
            if ($res) {
                throw new ResourceException('不能重复操作');
            }
        }
        // todo 主卡支付的订单退款应该退到主卡账号
        if ($action_type === UserFoodStamp::ACTION_TYPE_ORDER_RETURN) {
            $res = UserFoodStamp::where('type', UserFoodStamp::TYPE_GET)
                ->where('user_id', $user->id)
                ->where('order_id', $order->id)
                ->where('action_type', $action_type)
                ->lockForUpdate()
                ->first();
            if ($res) {
                throw new ResourceException('不能重复操作');
            }
        }
        $food_stamp_before = $user->food_stamp;
        $res = $user->increment('food_stamp', $food_stamp);
        $food_stamp_after = $user->fresh()->food_stamp;
        if ($res) {
            $user_food_stamp = new UserFoodStamp([
                'type'              => UserFoodStamp::TYPE_GET,
                'user_id'           => $user->id,
                'order_id'          => $order->id ?? 0,
                'order_no'          => $order->no ?? '',
                'master_card_no'    => $card_no,
                'food_stamp_before' => $food_stamp_before,
                'food_stamp'        => $food_stamp,
                'food_stamp_after'  => $food_stamp_after,
                'description'       => $description,
                'action_type'       => $action_type,
                'shop_id'           => $user->shop_id,
            ]);
            $user_food_stamp->save();
        }
        return true;
    }

    public function useFoodStamp($action_type, $user, $food_stamp, $card_no, $description = '', $order = null)
    {
        if (!$card_no) {
            throw new ResourceException('卡号不能为空');
        }
        $card_type = intval(substr($card_no, 0, 1));
        if (!in_array($card_type, [1, 2])) {
            throw new ResourceException('卡号异常');
        }
        if ($food_stamp <= 0) {
            throw new ResourceException('数量不能为0');
        }
        if (!in_array($action_type, array_flip(UserFoodStamp::$actionTypeMap))) {
            throw new ResourceException('操作类型错误');
        }
        if ($card_type == 1) {
            $user = User::query()
                ->where('id', $user->id)
                ->lockForUpdate()
                ->first();
            $food_stamp_before = $user->food_stamp;
            $res = $user->decreaseFoodStamp($food_stamp);
            $food_stamp_after = $user->fresh()->food_stamp;
            $master_card_no = $user->master_card_no;
            $secondary_card_no = '';

        } elseif ($card_type == 2) {
            $card = UserSecondaryCard::query()
                ->where('secondary_card_no', $card_no)
                ->where('user_id', $user->id)
                ->lockForUpdate()
                ->first();
            User::query()
                ->where('id', $card->masterUser->id)
                ->lockForUpdate()
                ->first();
            if (!$card) {
                throw new ResourceException('无效的卡号');
            }
            if ($card->is_close) {
                throw new ResourceException('该卡已关闭');
            }
            $food_stamp_before = $card->masterUser->food_stamp;
            $res = $card->masterUser->decreaseFoodStamp($food_stamp);
            $food_stamp_after = $card->masterUser->fresh()->food_stamp;
            $master_card_no = $card->masterUser->master_card_no;
            $secondary_card_no = $card_no;
        }

        if (!$res) {
            return false;
        }
        $user_food_stamp = new UserFoodStamp([
            'type'              => UserFoodStamp::TYPE_USE,
            'user_id'           => $user->id,
            'order_id'          => $order->id ?? 0,
            'order_no'          => $order->no ?? '',
            'food_stamp_before' => $food_stamp_before,
            'food_stamp'        => $food_stamp,
            'food_stamp_after'  => $food_stamp_after,
            'master_card_no'    => $master_card_no,
            'secondary_card_no' => $secondary_card_no,
            'description'       => $description,
            'action_type'       => $action_type,
            'shop_id'           => $user->shop_id,
        ]);
        $user_food_stamp->save();
        return true;
    }


}
