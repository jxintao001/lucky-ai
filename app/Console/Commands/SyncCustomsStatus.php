<?php

namespace App\Console\Commands;

use App\Models\CustomsNoStatus;
use App\Models\Order;
use App\Models\OrderCustomsLog;
use App\Models\User;
use App\Services\SplitOrderService;
use Illuminate\Console\Command;
use App\Models\Topic;
use App\Models\Reply;
use App\Models\ActiveUser;
use Carbon\Carbon;
use DB;

class SyncCustomsStatus extends Command
{

    protected $signature = 'hyie:sync-customs-status';
    protected $description = 'Sync customs status';

    public function __construct()
    {
        parent::__construct();
    }

    public function handle(SplitOrderService $splitOrderService)
    {
        $order = Order::find(1212);
        $this->info("Building");
        for ($i=1; $i < 1000000; $i++){
            $splitOrderService->split($order);
            if ($i%1000 == 0){
                $this->info("Processing ".$i);
            }
        }
        $this->info("Finished");
//        $before_time = strtotime("-1 week");
//        $items = OrderCustomsLog::where('synced', 0)
//            ->with('order')
//            ->where('created_at', '>=', $before_time)
//            ->limit(100000)
//            ->get();
//        if ($items) {
//            foreach ($items as $k => $item) {
//                // 获取清关状态
//                $customs_no_status = CustomsNoStatus::where('order_no', $item->order_no)->first();
//                if (!$customs_no_status || $item->order->customs_status === Order::CUSTOMS_STATUS_SUCCESS){
//                    continue;
//                }
//                // 更新订单清关状态
//                $item->order->customs_status = Order::CUSTOMS_STATUS_SUCCESS;
//                $item->order->customs_data = '身份信息错误';
//                $item->order->save();
//                // 标记已同步
//                $item->synced = 1;
//                $item->save();
//            }
//        }
    }


}
