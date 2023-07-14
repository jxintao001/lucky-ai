<?php

namespace App\Jobs;

use App\Models\Bargain;
use App\Models\GroupItem;
use App\Services\OrderService;
use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use App\Models\Group;
use App\Models\Order;

// 代表这个类需要被放到队列中执行，而不是触发时立即执行
class CloseGroup implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $group;

    public function __construct(Group $group, $delay)
    {
        $this->group = $group;
        // 设置延迟的时间，delay() 方法的参数代表多少秒之后执行
        $this->delay($delay);
    }

    // 定义这个任务类具体的执行逻辑
    // 当队列处理器从队列中取出任务时，会调用 handle() 方法
    public function handle()
    {
        // 判断对应的团购是否已经成功
        // 如果已经成功则不需要关闭订单，直接退出
        if ($this->group->finished_at) {
            return;
        }
        // 通过事务执行 sql
        \DB::transaction(function() {
            // 将团购的 status 字段标记为 fail，即关闭团购
            $this->group->update(['status' => Bargain::STATUS_FAIL,'closed' => true]);
            // 已付款的参团
            $group_items = GroupItem::where('group_id', $this->group->id)
                ->whereNotNull('paid_at')
                ->where('closed',false)
                ->get();
            // 退款
            $orderService = new OrderService();
            foreach ($group_items as $group_item){
                $group_item->update([
                    'status'    => GroupItem::STATUS_FAIL,
                    'closed'    => true
                ]);
                $order = Order::findOrFail($group_item->order_id);
                // 判断订单状态是否正确
                $orderService->refundOrder($order);
            }
        });
    }
}
