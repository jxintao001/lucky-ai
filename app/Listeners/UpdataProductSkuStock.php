<?php

namespace App\Listeners;

use App\Events\OrderPaid;
use App\Models\ProductSku;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use App\Models\OrderItem;

//  implements ShouldQueue 代表此监听器是异步执行的
class UpdataProductSkuStock implements ShouldQueue
{
    // Laravel 会默认执行监听器的 handle 方法，触发的事件会作为 handle 方法的参数
    public function handle(OrderPaid $event)
    {
        // 从事件对象中取出对应的订单
        $order = $event->getOrder();
        // 循环遍历订单的商品
        foreach ($order->items as $item) {
            $sku = ProductSku::find($item->product_sku_id);
            if ($sku->is_presale) {
                $sku->decreasePresale($item->amount);
            } else {
                $sku->decreaseStock($item->amount);
            }
        }
        // 组合商品sku减库存
        if ($order->cpItems->isNotEmpty()) {
            foreach ($order->cpItems as $cpItem) {
                $sku = $cpItem->productSku;
                $sku->decreaseStock($cpItem->amount);
            }
        }
        // 循环遍历订单的商品
        foreach ($order->items as $item) {
            $product = $item->product;
            // 计算对应商品的库存
            $stockCount = ProductSku::query()
                ->where('product_id', $product->id)
                ->sum('stock');
            // 更新商品总库存
            $product->update([
                'stock_count' => $stockCount,
            ]);
        }
    }
}
