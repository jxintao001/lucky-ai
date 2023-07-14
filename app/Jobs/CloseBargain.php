<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use App\Models\Bargain;

// 代表这个类需要被放到队列中执行，而不是触发时立即执行
class CloseBargain implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $bargain;

    public function __construct(Bargain $bargain, $delay)
    {
        $this->bargain = $bargain;
        // 设置延迟的时间，delay() 方法的参数代表多少秒之后执行
        $this->delay($delay);
    }

    // 定义这个任务类具体的执行逻辑
    // 当队列处理器从队列中取出任务时，会调用 handle() 方法
    public function handle()
    {
        // 判断对应的砍价是否已经被支付
        // 如果已经支付则不需要关闭砍价，直接退出
        if ($this->bargain->paid_at) {
            return;
        }
        // 通过事务执行 sql
        \DB::transaction(function() {
            // 将砍价的 status 字段标记为 fail，即关闭砍价
            $this->bargain->update(['status' => Bargain::STATUS_FAIL,'closed' => true]);
        });
    }
}
