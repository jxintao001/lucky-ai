<?php

namespace App\Services;

use App\Models\UserIntegral;
use Dingo\Api\Exception\ResourceException;

class UserIntegralService
{

    public function getIntegral($get_method, $user, $integral, $description = '', $order = null, $sku = null)
    {
        if ($integral < 1) {
            throw new ResourceException('积分数量不能小于1');
        }
        if (!in_array($get_method, array_flip(UserIntegral::$getMethodMap))) {
            throw new ResourceException('获取积分类型错误');
        }
        $res = $user->increment('integral', $integral);
        if ($res) {
            $user_integral = new UserIntegral([
                'type'        => UserIntegral::TYPE_GET,
                'user_id'     => $user->id,
                'order_id'    => $order->id ?? 0,
                'sku_id'      => $sku->id ?? 0,
                'integral'    => $integral,
                'description' => $description,
                'get_method'  => $get_method,
                'shop_id'     => $user->shop_id,
            ]);
            $user_integral->save();
        }
        return true;
    }

    public function useIntegral($use_method, $user, $integral, $description = '', $order = null, $sku = null)
    {
        if ($integral < 1) {
            return false;
        }
        if (!in_array($use_method, array_flip(UserIntegral::$useMethodMap))) {
            throw new ResourceException('获取积分类型错误');
        }
        $res = $user->decreaseIntegral($integral);
        if ($res) {
            $user_integral = new UserIntegral([
                'type'        => UserIntegral::TYPE_USE,
                'user_id'     => $user->id,
                'order_id'    => $order->id ?? 0,
                'sku_id'      => $sku->id ?? 0,
                'integral'    => $integral,
                'description' => $description,
                'use_method'  => $use_method,
                'shop_id'     => $user->shop_id,
            ]);
            $user_integral->save();
        }
        return true;
    }


}
