<?php

namespace App\Jobs;

use App\Models\CouponCode;
use App\Models\UserIntegral;
use App\Services\UserIntegralService;
use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use App\Models\Order;

// 代表这个类需要被放到队列中执行，而不是触发时立即执行
class CloseOrder implements ShouldQueue
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
        // 判断对应的订单是否已经被支付
        // 如果已经支付则不需要关闭订单，直接退出
        if ($this->order->paid_at || $this->order->closed) {
            return;
        }
        // 通过事务执行 sql
        \DB::transaction(function() {
            // 优惠券退还
            if ($this->order->couponCode) {
                $couponCode = CouponCode::find($this->order->couponCode->coupon_code_id);
                $couponCode->changeUsed(false);
                $this->order->couponCode->update(['used' => false,'used_at' => null]);
            }
            // 积分退还
            if ($this->order->integral_amount > 0) {
                $description = '订单积分退还';
                (new UserIntegralService())->getIntegral(UserIntegral::GET_METHOD_ORDER_EXCHANGE, $this->order->user, $this->order->integral_amount, $description, $this->order);
            }
            // 将订单的 closed 字段标记为 true，即关闭订单
            $this->order->update(['closed' => true, 'coupon_code_id' => null]);
        });
    }
}
