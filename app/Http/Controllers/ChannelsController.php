<?php

namespace App\Http\Controllers;


use App\Models\Channel;
use App\Models\Order;
use App\Models\UserAddress;
use App\Models\UserIdentity;
use App\Services\ChannelOrderService;
use App\Transformers\OrderTransformer;
use Dingo\Api\Exception\ResourceException;
use Dingo\Api\Routing\Helpers;
use Illuminate\Http\Request;
use League\Fractal\Manager as FractalManager;

class ChannelsController extends Controller
{
    use Helpers;

    public function __construct(FractalManager $fractal)
    {
        parent::__construct();
        $this->fractal = $fractal;
    }

    public function pushOrder(Request $request, ChannelOrderService $channelOrderService)
    {
        // 参数验证
        $channel_id = (int)$request->input('channel_id');
        $push_data = json_decode(trim($request->input('push_data')), true);
        //$sign = trim($request->input('sign'));
        if (!$channel_id) {
            throw new ResourceException('channel_id 参数不能为空');
        }
        if (!$push_data) {
            throw new ResourceException('push_data 参数不能为空');
        }
        if (!$sign) {
            //throw new ResourceException('sign 参数不能为空');
        }
        if (empty($push_data['orderNo'])) {
            throw new ResourceException('push_data.orderNo 参数不能为空');
        }
        $channel = Channel::find($channel_id);
        if (!$channel) {
            throw new ResourceException('channel_id 无效');
        }
        $order = Order::where(['no' => trim($push_data['orderNo'])])->exists();
        if ($order) {
            throw new ResourceException('push_data.orderNo 异常');
        }
        $channelOrderService->store($channel, $push_data, $sign);
        return $this->response()->created();
    }
    
    public function pushDelivered(Request $request, ChannelOrderService $channelOrderService)
    {
        
        // 参数验证
        $channel_id = (int)$request->input('channel_id');
        $push_data = json_decode(trim($request->input('push_data')), true);
        //$sign = trim($request->input('sign'));
        if (!$channel_id) {
            throw new ResourceException('channel_id 参数不能为空');
        }
        if (!$push_data) {
            throw new ResourceException('push_data 参数不能为空');
        }
        
        if (empty($push_data['orderNo'])) {
            throw new ResourceException('push_data.orderNo 参数不能为空');
        }
         if (empty($push_data['customs_status'])) {
            throw new ResourceException('push_data.customs_status 参数不能为空');
        }
        if (empty($push_data['ship_status'])) {
            throw new ResourceException('push_data.ship_status 参数不能为空');
        }
        if (empty($push_data['ship_data'])) {
            throw new ResourceException('push_data.ship_data 参数不能为空');
        }
        $channelOrderService->delivered($push_data);
        
    }
    

}
