<?php

namespace App\Services;

use App\Jobs\CloseGiftPackage;
use App\Models\GiftPackage;
use App\Models\GiftPackageItem;
use App\Models\GiftPackageReceive;
use App\Models\GiftPackageReceiveItem;
use App\Models\GiftPackageSet;
use App\Models\GiftPackageTemplate;
use App\Models\Order;
use App\Models\ProductSku;
use App\Models\UserFoodStamp;
use App\Models\UserGift;
use App\Models\UserGiftItem;
use Carbon\Carbon;
use Dingo\Api\Exception\ResourceException;
use Illuminate\Support\Str;

class GiftPackageService
{
    // 订单商品写入礼品库
    public function store($user, $request)
    {
        // 开启一个数据库事务
        return \DB::transaction(function () use ($user, $request) {
            // 写入礼品包信息表
            $gift_package = new GiftPackage($request->only([
                'type',
                'title',
                'wish_text',
                'wish_image',
                'wish_image2',
                'wish_audio',
                'wish_video',
                'template_id',
                'question',
                'answer',
            ]));
            $gift_package->user_id = $user->id;
            $gift_package->shop_id = $user->shop_id;
            if (!empty($request['order_id'])) {
                $order = Order::where('user_id', auth('api')->id())
                    ->where('id', $request['order_id'])
                    ->first();
                if (!$order) {
                    throw new ResourceException('无效的订单ID');
                }
                $gift_package->order_id = $request['order_id'];
            }
            if ($request['type'] === GiftPackage::TYPE_MANY_PEOPLE_SET) {
                $gift_package->set_count = $request['set_amount'];
            }
            if ($request['type'] === GiftPackage::TYPE_MANY_PEOPLE_SET || $request['type'] === GiftPackage::TYPE_MANY_PEOPLE) {
                $gift_package->receive_limit = !empty($request['receive_limit']) && $request['receive_limit'] > 0 ? intval($request['receive_limit']) : 1;
            }
            if (!empty($request['template_id']) && $template = GiftPackageTemplate::find($request['template_id'])) {
                $gift_package->wish_text = !empty($request['wish_text']) ? $request['wish_text'] : $template->title;
                $gift_package->wish_image = !empty($request['wish_image']) ? $request['wish_image'] : $template->image;
                $gift_package->wish_image2 = !empty($request['wish_image2']) ? $request['wish_image2'] : $template->image_preview;
                $gift_package->wish_audio = !empty($request['wish_audio']) ? $request['wish_audio'] : $template->audio;
                $gift_package->wish_video = !empty($request['wish_video']) ? $request['wish_video'] : $template->video;
            } elseif (!empty($request['wish_image'])) {
                $template = new GiftPackageTemplate([
                    'user_id'       => $user->id,
                    'season_id'     => 0,
                    'title'         => $request['wish_text'],
                    'image'         => $request['wish_image'],
                    'image_preview' => $request['wish_image2'],
                    'audio'         => $request['wish_audio'],
                    'video'         => $request['wish_video'],
                    'shop_id'       => $user->shop_id,
                ]);
                $template->save();
                $gift_package->template_id = $template->id;
            }
            $gift_package->save();
            // 写入礼品包礼品表
            foreach ($request['items'] as $k => $item) {
                $amount = $item['amount'];
                // 多人整套
                if ($request['type'] == GiftPackage::TYPE_MANY_PEOPLE_SET) {
                    $amount = $amount * $request['set_amount'];
                }
                if ($amount < 1) {
                    throw new ResourceException('礼品数不可小于1');
                }
                // 悲观锁
                $user_gift = UserGift::where('product_sku_id', $item['sku_id'])
                    ->where('user_id', auth('api')->id())
                    ->where('shop_id', $user->shop_id)
                    ->lockForUpdate()
                    ->first();
                if (!$user_gift) {
                    throw new ResourceException('礼品不存在');
                }
                // == todo ==
                if ($user_gift->count < $amount) {
                    throw new ResourceException('礼品数不足');
                }
                $sku = ProductSku::find($item['sku_id']);
                // 礼品扣减
                (new UserGiftService())->outStock(UserGiftItem::USE_METHOD_GIFT_PACKAGE, $user, $sku, $amount, $user_gift->sku_price, null, $gift_package);
                $gift_package_items = new GiftPackageItem([
                    'user_id'         => $user->id,
                    'product_id'      => $sku->product_id,
                    'product_sku_id'  => $sku->id,
                    'gift_package_id' => $gift_package->id,
                    'gift_count'      => $amount,
                    'price'           => $sku->price,
                    'sku_title'       => $sku->title,
                    'sku_image'       => $sku->product->cover ?? '',
                    'sku_price'       => $sku->price,
                ]);
                $gift_package_items->save();
                $gift_package->gift_count += $amount;
                $gift_package->start_at = Carbon::now();
                $gift_package->save();
                // 记录套装信息
                if ($request['type'] == GiftPackage::TYPE_MANY_PEOPLE_SET) {
                    $gift_package_set = new GiftPackageSet([
                        'user_id'         => $user->id,
                        'product_id'      => $sku->product_id,
                        'product_sku_id'  => $sku->id,
                        'gift_package_id' => $gift_package->id,
                        'amount'          => $item['amount'],
                        'sku_title'       => $sku->title,
                        'sku_image'       => $sku->product->cover ?? '',
                        'sku_price'       => $sku->price,
                    ]);
                    $gift_package_set->save();
                }
            }
            if (!empty($request['order_id']) && !empty($order)) {
                $order->gift_package_id = $gift_package->id;
                $order->gift_package_no = $gift_package->no;
                $order->gift_package_code = $gift_package->code;
                $order->save();
                // 订单对应的消费粮票记录
                $user_food_stamp = UserFoodStamp::query()
                    ->where('order_id', $request['order_id'])
                    ->where('type', UserFoodStamp::TYPE_USE)
                    ->where('user_id', $user->id)
                    ->where('gift_package_id', 0)
                    ->whereIn('action_type', [UserFoodStamp::ACTION_TYPE_ORDER_PAY, UserFoodStamp::ACTION_TYPE_SECONDARY_ORDER_PAY])
                    ->first();
                if ($user_food_stamp) {
                    $replace_type_str = GiftPackage::$typeMap[$request['type']] ?? '';
                    $user_food_stamp->gift_package_id = $gift_package->id;
                    if ($user_food_stamp->action_type === UserFoodStamp::ACTION_TYPE_ORDER_PAY) {
                        $user_food_stamp->action_type = UserFoodStamp::ACTION_TYPE_GIFT_ORDER_PAY;
                        $user_food_stamp->description = Str::replaceFirst('购买-', '送礼·' . $replace_type_str . '-', $user_food_stamp->description);
                    } elseif ($user_food_stamp->action_type === UserFoodStamp::ACTION_TYPE_SECONDARY_ORDER_PAY) {
                        $user_food_stamp->action_type = UserFoodStamp::ACTION_TYPE_SECONDARY_GIFT_ORDER_PAY;
                        $user_food_stamp->description = Str::replaceFirst('副卡购买-', '副卡送礼·' . $replace_type_str . '-', $user_food_stamp->description);
                    }
                    $user_food_stamp->save();
                }

            }
            dispatch(new CloseGiftPackage($gift_package, config('app.gift_package_ttl')));
            return $gift_package;
        });
    }

    // 礼包领取
    public function receive($user, $gift_package, $answer = '')
    {
        // 开启一个数据库事务
        return \DB::transaction(function () use ($user, $gift_package, $answer) {
            $gift_package = GiftPackage::where('id', $gift_package->id)
                ->lockForUpdate()
                ->first();
            $receive = GiftPackageReceive::where('gift_package_id', $gift_package->id)
                ->where('user_id', $user->id)
                ->lockForUpdate()
                ->first();
            if (($gift_package->answer !== '' || !is_null($gift_package->answer)) && trim($gift_package->answer) !== trim($answer)) {
                throw new ResourceException('答案错误');
            }
            if ($receive) {
                throw new ResourceException('领过了');
            }
            if ($gift_package->status === GiftPackage::STATUS_EXPIRED) {
                throw new ResourceException('过期了');
            }
            if ($gift_package->status === GiftPackage::STATUS_FINISH) {
                throw new ResourceException('领完了');
            }
            if ($gift_package->closed) {
                throw new ResourceException('关闭了');
            }
            $receive = new GiftPackageReceive([
                'user_id'         => $user->id,
                'gift_package_id' => $gift_package->id,
                'receive_count'   => 0,
                'shop_id'         => $user->shop_id,
            ]);
            $receive->save();
            $gifts = $this->getRandomGifts($gift_package);

            if (!$gifts) {
                throw new ResourceException('领完了');
            }
            foreach ($gifts as $sku_id => $amount) {
                $gift_package_item = GiftPackageItem::where('gift_package_id', $gift_package->id)
                    ->where('product_sku_id', $sku_id)
                    ->lockForUpdate()
                    ->first();
                if (!$gift_package_item) {
                    throw new ResourceException('礼品不存在');
                }
                $remaining = $gift_package_item->gift_count - $gift_package_item->receive_count;
                if ($remaining <= 0) {
                    throw new ResourceException('领完了');
                }
                if ($remaining < $amount) {
                    throw new ResourceException('礼品数不足');
                }
                $sku = ProductSku::find($sku_id);
                // 礼品扣减
                (new UserGiftService())->inStock(UserGiftItem::GET_METHOD_GIFT_PACKAGE, $user, $sku, $amount, $sku->price, null, $gift_package);
                $gift_package_receive_items = new GiftPackageReceiveItem([
                    'user_id'         => $user->id,
                    'receive_id'      => $receive->id,
                    'product_id'      => $sku->product_id,
                    'product_sku_id'  => $sku->id,
                    'gift_package_id' => $gift_package->id,
                    'receive_count'   => $amount,
                    'sku_title'       => $sku->title,
                    'sku_image'       => $sku->product->cover ?? '',
                    'sku_price'       => $sku->price,
                ]);
                $gift_package_receive_items->save();
                $receive->receive_count += $amount;
                $receive->save();
                $gift_package->gift_receive_count += $amount;
                $gift_package->save();
                $gift_package_item->receive_count += $amount;
                $gift_package_item->save();
            }
            // 套装领取数量
            if ($gift_package->type === GiftPackage::TYPE_MANY_PEOPLE_SET) {
                $gift_package->set_receive_count += $gift_package->receive_limit;
                $gift_package->save();
            }
            $gift_package_items = GiftPackageItem::where('gift_package_id', $gift_package->id)
                ->lockForUpdate()
                ->get();
            if ($gift_package_items) {
                $finished = true;
                foreach ($gift_package_items as $k => $item) {
                    if (($item->gift_count - $item->receive_count) > 0) {
                        $finished = false;
                    }
                }
                if ($finished) {
                    $gift_package->status = GiftPackage::STATUS_FINISH;
                    $gift_package->closed = 1;
                    $gift_package->finished_at = Carbon::now();
                    $gift_package->save();
                }
            }
            // 返回当前用户
            $received_gifts = GiftPackageReceiveItem::where('gift_package_id', $gift_package->id)
                ->where('user_id', $user->id)
                ->get();
            return $received_gifts;
        });
    }

    public function getRandomGifts($gift_package)
    {
        $gifts = [];
        $items = [];
        foreach ($gift_package->items as $k => $item) {
            $remaining = $item->gift_count - $item->receive_count;
            if ($remaining <= 0) {
                continue;
            }
            for ($i = 0; $i < $remaining; $i++) {
                $items[] = $item->toArray();
            }
        }
        if ($gift_package->type === GiftPackage::TYPE_SINGLE_PEOPLE) {
            $gifts = $items;
        } elseif ($gift_package->type === GiftPackage::TYPE_MANY_PEOPLE) {
            $gifts = collect($items)->random($gift_package->receive_limit)->toArray();
        } elseif ($gift_package->type === GiftPackage::TYPE_MANY_PEOPLE_SET) {
            foreach ($gift_package->sets as $k => $item) {
                if ($item->amount <= 0 || $gift_package->receive_limit <= 0) {
                    continue;
                }
                for ($i = 0; $i < $item->amount * $gift_package->receive_limit; $i++) {
                    $gifts[] = $item->toArray();
                }
            }
        }
        if (!$gifts) {
            return $gifts;
        }
        $temp_gifts = [];
        foreach ($gifts as $k => $gift) {
            if (isset($temp_gifts[$gift['product_sku_id']])) {
                $temp_gifts[$gift['product_sku_id']]++;
            } else {
                $temp_gifts[$gift['product_sku_id']] = 1;
            }
        }
        return $temp_gifts;
    }

    // 礼盒礼品退还
    public function returnGiftPackage($gift_package)
    {
        // 开启一个数据库事务
        return \DB::transaction(function () use ($gift_package) {
            $gift_package = GiftPackage::where('id', $gift_package->id)
                ->lockForUpdate()
                ->first();
            if ($gift_package->status === GiftPackage::STATUS_EXPIRED) {
                throw new ResourceException('过期了');
            }
            if ($gift_package->status === GiftPackage::STATUS_FINISH) {
                throw new ResourceException('领完了');
            }
            if ($gift_package->closed) {
                throw new ResourceException('关闭了');
            }
            $gift_package_items = GiftPackageItem::where('gift_package_id', $gift_package->id)
                ->lockForUpdate()
                ->get();

            foreach ($gift_package_items as $k => $item) {
                $remaining = $item->gift_count - $item->receive_count;
                if ($remaining <= 0) {
                    continue;
                }
                $sku = ProductSku::find($item->product_sku_id);
                (new UserGiftService())->inStock(UserGiftItem::GET_METHOD_GIFT_PACKAGE_RETURN, $gift_package->user, $sku, $remaining, $sku->price, null, $gift_package);
            }
            $gift_package->status = GiftPackage::STATUS_EXPIRED;
            $gift_package->closed = 1;
            $gift_package->save();
            return $gift_package;
        });
    }
}
