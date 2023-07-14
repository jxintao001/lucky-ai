<?php

namespace App\Http\Controllers;


use App\Handlers\ImageUploadSftpHandler;
use App\Jobs\OffiaccountLog;
use App\Jobs\OffiaccountAdvertiserLog;
use App\Models\CarPad;
use App\Models\Shop;
use App\Models\Advertiser;
use EasyWeChat\Factory;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Storage;


class ServicesController extends Controller
{
    public function wxacode(ImageUploadSftpHandler $uploader)
    {

        $shop = Shop::findOrFail(request('shop_id', config('app.shop_id')));
        $appid = !empty(request('appid')) ? request('appid') : $shop->wechat_app_id;
        $secret = !empty(request('secret')) ? request('secret') : $shop->wechat_app_secret;
        $path = request('path');
        if (!$path) {
            return $this->errorBadRequest('path不能为空值');
        }
        if (!$appid || !$secret) {
            return $this->errorBadRequest('请求异常');
        }
//        if ($shop->qr_code) {
//            $data['wxacode'] = config('api.img_host') . $shop->qr_code;
//            return $data;
//        }
        $config = [
            'app_id'        => $appid,
            'secret'        => $secret,
            // 下面为可选项
            // 指定 API 调用返回结果的类型：array(default)/collection/object/raw/自定义类名
            'response_type' => 'array',

            'log' => [
                'level' => 'error',
                'file'  => storage_path('logs/wechat.log'),
            ],
        ];

        $app = Factory::miniProgram($config);

        $response = $app->app_code->get($path);
        $data = [];
        if ($response instanceof \EasyWeChat\Kernel\Http\StreamResponse) {
            $file = $response->getBodyContents();
            // 保存图片到本地
            //$result = $uploader->saveStream($file);
            //$data['wxacode']  = !empty($result) ? config('api.img_host').$result : '';
            //$data['wxacode']  = !empty($result) ? "https://santa-image.santaluma.com/".$result : '';

            $filename = 'images/wxacode/' . str_random(32) . '.jpg';
            $disk = Storage::disk('cosv5');
            $file_content = $disk->put($filename, $file);//第一个参数是你储存桶里想要放置文件的路径，第二个参数是文件对象
            $file_url = $disk->url($file_content);//获取到文件的线上地址
            $data['wxacode'] = !empty($file_url) ? config('api.img_host') . $filename : '';
        }
        return $data;
    }

    public function offiaccount()
    {
        $official_account = app('official_account');
        $official_account->server->push(function ($message) use ($official_account) {
            switch ($message['MsgType']) {
                case 'event':
                    if (!empty($message['EventKey']) && !empty($message['FromUserName'])) {
                        $event_key = str_replace('qrscene_', '', $message['EventKey']);
                        $user = $official_account->user->get($message['FromUserName']);
                        if ($user['subscribe'] == 1) {
                            Cache::add(CarPad::$power_switch_cache_prefix . $event_key, 1, CarPad::$power_switch_cache_expiration); // 15分钟
                            //return 'pad_no：' . $event_key . '  openId：' . $message['FromUserName'] . ' 充电开启';
                        }
                    }
                    $user = $user ?? [];
                    dispatch(new OffiaccountLog($message, $user, 1));
                    return '终于等到您，懂生活的时髦精！欢迎加入“鲤鱼商汇”家族～在这里，你可以找到全球时尚单品、居家生活好物！底价正品 超值优惠！

快来体验属于你的品质生活，马上开逛吧～';
                    break;
                case 'text':
                    return '收到文字消息';
                    break;
                case 'image':
                    return '收到图片消息';
                    break;
                case 'voice':
                    return '收到语音消息';
                    break;
                case 'video':
                    return '收到视频消息';
                    break;
                case 'location':
                    return '收到坐标消息';
                    break;
                case 'link':
                    return '收到链接消息';
                    break;
                case 'file':
                    return '收到文件消息';
                // ... 其它消息
                default:
                    return '收到其它消息';
                    break;
            }
        });
        ob_clean();
        return $official_account->server->serve();

    }
    
    public function offiaccount_advertisers($id)
    {
        $advertisers = Advertiser::findOrFail($id);
        //print_r($advertisers->name);exit;
        //$official_account = config('wechat.official_account.default');
        $config = array(
                        'app_id'  => $advertisers->official_account_appid,
                        'secret'  => $advertisers->official_account_secret,
                        'token'   => $advertisers->official_account_token,
                        'aes_key' => $advertisers->official_account_aeskey,
                        );
        $msg_type_event = $advertisers->msg_type_event;
        $official_account = Factory::officialAccount($config);
        
        $official_account->server->push(function ($message) use ($official_account,$msg_type_event, $id) {
            switch ($message['MsgType']) {
                case 'event':
//                    if (!empty($message['EventKey']) && !empty($message['FromUserName'])) {
//                        $event_key = str_replace('qrscene_', '', $message['EventKey']);
//                        $user = $official_account->user->get($message['FromUserName']);
//                        if ($user['subscribe'] == 1) {
//                            Cache::add(CarPad::$power_switch_cache_prefix . $event_key, 1, CarPad::$power_switch_cache_expiration); // 15分钟
//                            //return 'pad_no：' . $event_key . '  openId：' . $message['FromUserName'] . ' 充电开启';
//                        }
//                    }
//                    $user = $user ?? [];
                    
                    $user = $official_account->user->get($message['FromUserName']);
                    $user = $user ?? [];
                    
                    dispatch(new OffiaccountAdvertiserLog($message, $user, $id, 1));
                    
//                    \DB::transaction(function () {
//                    $log = new OfficialAccountAdvertiserLog([
//                                'wechat_openid'  =>  '',
//                                'wechat_unionid' => '',
//                                'to_username'    => '',
//                                'msg_type'       => '',
//                                'event'          => '',
//                                'event_key'      => '',
//                                'user_data'      => '',
//                                'message_data'   => '',
//                                'shop_id'        => 0,
//                                'advertiser_id'  => 2,
//                                ]);
//                            $log->wechat_unionid = '';
//                            $log->user_data =  '';
//                            $log->save();
//                    });
                    
                    
                    $msg_type_event = '<a href="https://mp.weixin.qq.com/s/j54Sta2T6TNmyLIV7LJM9w">测试</a>';
                    
//                    $user = $official_account->user->get($message['FromUserName']);
//                    return json_encode($user);
                    return $msg_type_event;
                    break;
                case 'text':
//                    $tt = new Message\News([
//                        'title'=>"一个失败的网恋故事",
//                        'url'=>'http://s3sy.com/show/71',
//                        'description'=>'就是2017年十一月份的某一天晚上，在距离那么远的他，和我在一起了，现在想来，都感到很不可思议的样子。',
//                        'image'=>'http://image.s3sy.com/iigMws8sJTHKm6hu5oFJcVOVwYPYPIKB18mR6cZd.jpeg'
//                    ]);
//
//                    return $tt;
                    
                    return '收到文字消息';
                    break;
                case 'image':
                    return '收到图片消息';
                    break;
                case 'voice':
                    return '收到语音消息';
                    break;
                case 'video':
                    return '收到视频消息';
                    break;
                case 'location':
                    return '收到坐标消息';
                    break;
                case 'link':
                    return '收到链接消息';
                    break;
                case 'file':
                    return '收到文件消息';
                // ... 其它消息
                default:
                    return '收到其它消息';
                    break;
            }
        });
        ob_clean();
        return $official_account->server->serve();

    }
}
