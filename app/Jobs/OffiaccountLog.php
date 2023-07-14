<?php

namespace App\Jobs;

use App\Models\CarPad;
use App\Models\OfficialAccountLog;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

// 代表这个类需要被放到队列中执行，而不是触发时立即执行
class OffiaccountLog implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $user_data;
    protected $message_data;

    public function __construct($message_data, $user_data, $delay)
    {
        $this->user_data = $user_data;
        $this->message_data = $message_data;
        $this->delay($delay);
    }

    public function handle()
    {
        if (empty($this->message_data['FromUserName']) || empty($this->message_data['Event'])) {
            return;
        }
        // 通过事务执行 sql
        \DB::transaction(function () {
            $log = new OfficialAccountLog([
                'wechat_openid'  => $this->message_data['FromUserName'] ?? '',
                'wechat_unionid' => '',
                'to_username'    => $this->message_data['ToUserName'] ?? '',
                'msg_type'       => $this->message_data['MsgType'] ?? '',
                'event'          => $this->message_data['Event'] ?? '',
                'event_key'      => $this->message_data['EventKey'] ?? '',
                'user_data'      => '',
                'message_data'   => $this->message_data,
                'shop_id'        => 0,
            ]);
            if (!empty($this->message_data['EventKey']) && $event_key = str_replace('qrscene_', '', $this->message_data['EventKey'])) {
                $car_pad = CarPad::where('pad_no', $event_key)->first();
                if ($car_pad && !empty($car_pad->shop_id)) {
                    $log->shop_id = $car_pad->shop_id;
                }
            }
            $log->wechat_unionid = $this->user_data['unionid'] ?? '';
            $log->user_data = !empty($this->user_data) ? $this->user_data : '';
            // 创建用户
            if (!empty($this->user_data['unionid'])) {
                $user = User::whereWechatOpenid($this->message_data['FromUserName'])->lockForUpdate()->first();
                if (!$user) {
                    $inviter_id = 0;
                    if (request('inviter_id')) {
                        $inviter = User::find(intval(request('inviter_id')));
                        $inviter_id = $inviter ? $inviter->id : 0;
                    }
                    $config = config('wechat.official_account.default');
                    $newUser = [
                        'wechat_appid'    => $config['app_id'] ?? '',
                        'wechat_openid'   => $this->message_data['FromUserName'],
                        'wechat_unionid'  => $this->user_data['unionid'],
                        'name'            => $this->user_data['nickname'] ?? '',
                        'gender'          => $this->user_data['sex'] ?? 0,
                        'language'        => $this->user_data['language'] ?? '',
                        'city'            => $this->user_data['city'] ?? '',
                        'province'        => $this->user_data['province'] ?? '',
                        'country'         => $this->user_data['country'] ?? '',
                        'avatar'          => $this->user_data['headimgurl'] ?? '',
                        'level'           => 1,
                        'register_source' => 'wechat_official_account',
                        'shop_id'         => $car_pad->shop_id ?? 0,
                        'last_shop_id'    => $car_pad->shop_id ?? 0,
                        'inviter_id'      => $inviter_id,
                        'last_actived_at' => Carbon::now()
                    ];
                    User::create($newUser);
                } else {
                    $user->last_shop_id = $car_pad->shop_id ?? 0;
                    $user->last_actived_at = Carbon::now();
                    $user->save();
                }
            }
            $log->save();
        });
    }
}
