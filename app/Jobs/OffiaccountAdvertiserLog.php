<?php

namespace App\Jobs;

use App\Models\CarPad;
use App\Models\OfficialAccountAdvertiserLog;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

// 代表这个类需要被放到队列中执行，而不是触发时立即执行
class OffiaccountAdvertiserLog implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $id;
    protected $user_data;
    protected $message_data;
    
    public function __construct($message_data, $user_data, $id, $delay)
    {
        $this->user_data = $user_data;
        $this->message_data = $message_data;
        $this->id = $id;
        $this->delay($delay);
    }

    public function handle()
    {
        if (empty($this->message_data['FromUserName']) || empty($this->message_data['Event'])) {
            return;
        }
        // 通过事务执行 sql
        \DB::transaction(function () {
            $log = new OfficialAccountAdvertiserLog([
                'wechat_openid'  => $this->message_data['FromUserName'] ?? '',
                'wechat_unionid' => '',
                'to_username'    => $this->message_data['ToUserName'] ?? '',
                'msg_type'       => $this->message_data['MsgType'] ?? '',
                'event'          => $this->message_data['Event'] ?? '',
                'event_key'      => $this->message_data['EventKey'] ?? '',
                'user_data'      => '',
                'message_data'   => $this->message_data,
                'shop_id'        => 0,
                'advertiser_id'  => $this->id,
            ]);
            if (!empty($this->message_data['EventKey']) && $event_key = str_replace('qrscene_', '', $this->message_data['EventKey'])) {
                $car_pad = CarPad::where('pad_no', $event_key)->first();
                if ($car_pad && !empty($car_pad->shop_id)) {
                    $log->shop_id = $car_pad->shop_id;
                }
            }
            $log->wechat_unionid = $this->user_data['unionid'] ?? '';
            $log->user_data = !empty($this->user_data) ? $this->user_data : '';
            $log->save();

        });
    }
}
