<?php

namespace App\Jobs;

use App\Handlers\RabbitMqPublishHandler;
use App\Services\CustomsService;
use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use App\Models\Order;

// 代表这个类需要被放到队列中执行，而不是触发时立即执行
class CustomsRefund implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $order;

    public function __construct(Order $order, $delay)
    {
        $this->order = $order;
        // 设置延迟的时间，delay() 方法的参数代表多少秒之后执行
        $this->delay($delay);
    }

    // 定义这个任务类具体的执行逻辑
    // 当队列处理器从队列中取出任务时，会调用 handle() 方法
    public function handle()
    {
        // 判断对应的订单是否符合报关条件
        if (!$this->order->paid_at) {
            return;
        }
        $rabbitMqPublishHandler = new RabbitMqPublishHandler();
        $customsService = new CustomsService($rabbitMqPublishHandler);

        $customsService->refund($this->order);
    }
}
