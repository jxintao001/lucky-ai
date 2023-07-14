<?php

namespace App\Jobs;

use App\Models\GiftPackage;
use App\Services\GiftPackageService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

// 代表这个类需要被放到队列中执行，而不是触发时立即执行
class CloseGiftPackage implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $gift_package;

    public function __construct(GiftPackage $giftPackage, $delay)
    {
        $this->gift_package = $giftPackage;
        // 设置延迟的时间，delay() 方法的参数代表多少秒之后执行
        $this->delay($delay);
    }

    // 定义这个任务类具体的执行逻辑
    // 当队列处理器从队列中取出任务时，会调用 handle() 方法
    public function handle()
    {
        if ($this->gift_package->closed || $this->gift_package->status !== GiftPackage::STATUS_NORMAL) {
            return;
        }
        (new GiftPackageService())->returnGiftPackage($this->gift_package);
    }
}
