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
use Carbon\Carbon;
use Dingo\Api\Exception\ResourceException;

class BargainService
{
    public function add(User $user, ProductSku $sku)
    {
        // 开启事务
        $bargain = \DB::transaction(function () use ($user, $sku) {
            //dd($sku->product->bargain->target_price);
            // 创建一个砍价
//            $price = $sku->tax_price;
//            if($user->level == 2){
//                $price = $sku->club_tax_price;
//            }
            $bargain = new Bargain([
                'product_id'    => $sku->product_id,
                'sku_id'        => $sku->id,
                'target_price'  => $sku->product->bargain->target_price,
                'current_price' => $sku->original_price,
                'price'         => $sku->original_price,
                'user_count'    => 0,
                'status'        => Bargain::STATUS_PENDING,
            ]);
            // 砍价关联到当前用户
            $bargain->user()->associate($user);
            // 写入数据库
            $bargain->save();
            if ($sku->decreaseStock(1) <= 0) {
                throw new InvalidRequestException('该商品库存不足');
            }
            return $bargain;
        });

        // 定时关闭砍价
        dispatch(new CloseBargain($bargain, config('app.bargain_ttl')));

        return $bargain;
    }

    public function cut(User $user, Bargain $bargain)
    {
        $bargain->checkAvailable($user, $bargain);
        // 开启一个数据库事务
        $bargain_item = \DB::transaction(function () use ($user, $bargain) {
            $bargain_item = new BargainItem([
                'bargain_id'    => $bargain->id,
                'current_price' => $bargain->current_price,
                'cut_price'     => $bargain->randomCutPrice($bargain)
            ]);
            // 砍价关联到当前用户
            $bargain_item->user()->associate($user);
            // 写入数据库
            $bargain_item->save();
            if ($bargain_item->cut_price > 0) {
                $current_price = $bargain->current_price - $bargain_item->cut_price;
                $bargain->update([
                    'current_price' => $current_price,
                    'user_count' => $bargain->user_count+1,
                    'status' => Bargain::STATUS_WAITING
                ]);
            }
            if ((string)$bargain->current_price <= (string)$bargain->target_price){
                $bargain->update([
                    'status' => Bargain::STATUS_SUCCESS
                ]);
            }
            return $bargain_item;
        });

        // 这里我们直接使用 dispatch 函数
        //dispatch(new CloseOrder($order, config('app.order_ttl')));

        return $bargain_item;
    }


}
