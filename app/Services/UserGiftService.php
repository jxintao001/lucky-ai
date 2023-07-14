<?php

namespace App\Services;

use App\Models\Order;
use App\Models\UserGift;
use App\Models\UserGiftItem;
use Carbon\Carbon;
use Dingo\Api\Exception\ResourceException;

class UserGiftService
{
    // 订单商品写入礼品库
    public function orderToGift($order_id)
    {
        // 开启一个数据库事务
        return \DB::transaction(function () use ($order_id) {
            // 悲观锁
            $order = Order::where('id', $order_id)
                ->lockForUpdate()
                ->first();
            if (!$order || empty($order->items)) {
                throw new ResourceException('订单不存在');
            }
            // == todo ==
            if ($order->closed) {
                throw new ResourceException('订单已关闭');
            }
            if (!$order->paid_at) {
                throw new ResourceException('订单未付款');
            }
            // 通过交付时间判断是否已经交付
            if ($order->delivered_at) {
                throw new ResourceException('订单商品不能重复导入礼品库');
            }
            // 循环写入礼品库
            foreach ($order->items as $k => $item) {
                $this->inStock(UserGiftItem::GET_METHOD_ORDER, $order->user, $item->productSku, $item->amount, $item->price, $order);
                //$this->outStock(UserGiftItem::USE_METHOD_EXCHANGE, $order->user, $item->productSku, $item->amount, $item->price, $order);
            }
            // 更新交付方式和时间
            $order->delivery_method = Order::DELIVERY_METHOD_GIFT;
            $order->delivered_at = Carbon::now();
            $order->save();
            return $order;
        });
    }

    public function inStock($get_method, $user, $sku, $amount, $price = 0, $order = null, $giftPackage = null)
    {
        if ($amount < 1) {
            throw new ResourceException('数量不可小于1');
        }
        if (!in_array($get_method, array_flip(UserGiftItem::$getMethodMap))) {
            throw new ResourceException('导入礼品库类型错误');
        }
        $user_gift = UserGift::where('user_id', $user->id)
            ->where('product_id', $sku->product_id)
            ->where('product_sku_id', $sku->id)
            ->where('shop_id', $user->shop_id)
            ->lockForUpdate()
            ->first();
        if ($user_gift) {
            $res = $user_gift->increment('count', $amount);
        } else {
            $user_gift = new UserGift([
                'user_id'        => $user->id,
                'product_id'     => $sku->product_id,
                'product_sku_id' => $sku->id,
                'sku_title'      => $sku->title,
                'sku_image'      => $sku->product->cover ?? '',
                'sku_price'      => $sku->price,
                'count'          => $amount,
                'shop_id'        => $user->shop_id,
            ]);
            $res = $user_gift->save();
        }
        if ($res) {
            // 写入获取礼品记录
            $user_gift_item = new UserGiftItem([
                'type'            => UserGiftItem::TYPE_GET,
                'user_id'         => $user->id,
                'order_id'        => $get_method === UserGiftItem::GET_METHOD_ORDER ? $order->id : 0,
                'gift_package_id' => ($get_method === UserGiftItem::GET_METHOD_GIFT_PACKAGE || $get_method === UserGiftItem::GET_METHOD_GIFT_PACKAGE_RETURN) ? $giftPackage->id : 0,
                'product_id'      => $sku->product_id,
                'product_sku_id'  => $sku->id,
                'sku_title'       => $sku->title,
                'sku_image'       => $sku->product->cover ?? '',
                'sku_price'       => $sku->price,
                'amount'          => $amount,
                'count'           => $amount,
                'price'           => $price,
                'get_method'      => $get_method,
                'shop_id'         => $user->shop_id,
            ]);
            $user_gift_item->save();
        }
    }

    public function outStock($use_method, $user, $sku, $amount, $price = 0, $order = null, $giftPackage = null)
    {
        if ($amount < 1) {
            throw new ResourceException('数量不可小于1');
        }
        if (!in_array($use_method, array_flip(UserGiftItem::$useMethodMap))) {
            throw new ResourceException('导出礼品库类型错误');
        }
        $user_gift = UserGift::where('user_id', $user->id)
            ->where('product_id', $sku->product_id)
            ->where('product_sku_id', $sku->id)
            ->where('shop_id', $user->shop_id)
            ->lockForUpdate()
            ->first();
        if (!$user_gift || $user_gift->count < $amount) {
            throw new ResourceException('礼品库不存在或数量不足');
        }
        $res = $user_gift->decrement('count', $amount);
        // 写入获取礼品记录
        if (!$res) {
            throw new ResourceException('礼品扣减失败');
        }
        // 礼品库明细扣减
        $user_gift_item = UserGiftItem::where('user_id', $user->id)
            ->where('type', UserGiftItem::TYPE_GET)
            ->where('product_id', $sku->product_id)
            ->where('product_sku_id', $sku->id)
            ->where('shop_id', $user->shop_id)
            ->where('count', '>', 0)
            ->orderBy('id', 'asc')
            ->lockForUpdate()
            ->take($amount)
            ->get();
        $temp_amount = $amount;
        foreach ($user_gift_item as $k => $item) {
            if ($temp_amount == 0){
                break;
            }
            if ($item->count >= $temp_amount) {
                $item->decrement('count', $temp_amount);
                break;
            } else {
                $temp_amount -= $item->count;
                $item->decrement('count', $item->count);
            }
        }
        $user_gift_item = new UserGiftItem([
            'type'            => UserGiftItem::TYPE_USE,
            'user_id'         => $user->id,
            'order_id'        => $use_method === UserGiftItem::USE_METHOD_EXCHANGE ? $order->id : 0,
            'gift_package_id' => ($use_method === UserGiftItem::USE_METHOD_GIFT_PACKAGE || $use_method === UserGiftItem::USE_METHOD_GIVE_AWAY) ? $giftPackage->id : 0,
            'product_id'      => $sku->product_id,
            'product_sku_id'  => $sku->id,
            'sku_title'       => $sku->title,
            'sku_image'       => $sku->product->cover ?? '',
            'sku_price'       => $sku->price,
            'amount'          => $amount,
            'price'           => $price,
            'use_method'      => $use_method,
            'shop_id'         => $user->shop_id,
        ]);
        if (!$user_gift_item->save()) {
            throw new ResourceException('礼品扣减记录保存失败');
        }
        return true;
    }


}
