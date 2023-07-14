<?php

namespace App\Services;

use App\Models\CartItem;
use Auth;

class CartService
{
    public function get()
    {
        $banned_products = Auth::user()->shop->banned_products;
        return Auth::user()->cartItems($banned_products)->paginate(per_page());
    }

    public function add($skuId, $amount)
    {
        //DB::connection()->enableQueryLog(); // 开启查询日志

        $user = Auth::user();
        // 从数据库中查询该商品是否已经在购物车中
        if ($item = $user->cartItems()->where('product_sku_id', $skuId)->first()) {
            // 如果存在则直接叠加商品数量
            $item->pivot->update([
                'amount' => $item->pivot->amount + $amount,
            ]);
        } else {
            // 否则创建一个新的购物车记录
            $item = new CartItem(['amount' => $amount]);
            $item->user()->associate($user);
            $item->productSku()->associate($skuId);
            $item->save();
        }
        //print_r(DB::getQueryLog());exit();
        return $item;
    }

    public function dec($skuId, $amount)
    {
        $user = Auth::user();
        // 从数据库中查询该商品是否已经在购物车中
        if ($item = $user->cartItems()->where('product_sku_id', $skuId)->first()) {
            $amount = $item->pivot->amount > $amount ? $amount : $item->pivot->amount;
            // 如果存在则直接叠加商品数量
            $item->pivot->update([
                'amount' => $item->pivot->amount - $amount,
            ]);
        }

        return $item;
    }

    public function input($skuId, $amount)
    {
        $user = Auth::user();
        // 从数据库中查询该商品是否已经在购物车中
        if ($item = $user->cartItems()->where('product_sku_id', $skuId)->first()) {
            // 如果存在则直接叠加商品数量
            $item->pivot->update([
                'amount' => $amount,
            ]);
        } else {
            // 否则创建一个新的购物车记录
            $item = new CartItem(['amount' => $amount]);
            $item->user()->associate($user);
            $item->productSku()->associate($skuId);
            $item->save();
        }

        return $item;
    }

    public function remove($skuIds)
    {
        if (!is_array($skuIds)) {
            $skuIds = explode(',', $skuIds);
        }
        Auth::user()->cartItems()->detach($skuIds);

    }
}
