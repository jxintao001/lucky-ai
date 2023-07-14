<?php

namespace App\Services;

use App\Models\Order;
use App\Models\Post;
use App\Models\User;

class PostService
{
    public function add(User $user, Order $order = null)
    {
        // 开启事务
        $post = \DB::transaction(function () use ($user, $order) {
            $product_ids = ($order && $order->items) ? '-' . implode('-', $order->items->pluck('product_id')->toArray()) . '-' : '';
            $product_sku_ids = ($order && $order->items) ? '-' . implode('-', $order->items->pluck('product_sku_id')->toArray()) . '-' : '';
            $title = '我购买了 ';
            $product_skus = [];
            if ($order->items) {
                foreach ($order->items as $item) {
                    $product = $item->product;
                    $product_sku = $item->productSku;
                    $title .= $product_sku->title . ' ';
                    $product_skus[] = [
                        'product_id'        => $product->id,
                        'product_type'      => $product->type,
                        'product_cover'     => $product->cover,
                        'product_sku_id'    => $item->product_sku_id,
                        'product_sku_price' => $item->price,
                        'product_sku_title' => $product_sku->title,
                    ];
                }
            }
            $post = new Post([
                'title'           => $title,
                'content'         => $title,
                'user_id'         => $user->id,
                'order_id'        => $order->id,
                'product_ids'     => $product_ids,
                'product_sku_ids' => $product_sku_ids,
                'product_skus'    => $product_skus
            ]);
            // 写入数据库
            $post->save();
        });
        return $post;
    }


}
