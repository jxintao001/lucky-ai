<?php

namespace App\Jobs;


use GuzzleHttp\Client;
use App\Handlers\RabbitMqPublishHandler;
use App\Services\CustomsService;
use App\Models\OrderCustomsLog;
use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use App\Models\Order;
use App\Models\CouponCode;
use App\Models\CustomsPay;

// 代表这个类需要被放到队列中执行，而不是触发时立即执行
class CustomsHyie implements ShouldQueue
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
        if (!$this->order->paid_at || $this->order->closed) {
            return;
        }
        $data = $this->buildOrder($this->order);
        $items = $this->buildOrderItems($this->order);
        $data['orderList'] = $items;
        $json_data = json_encode($data);
        //print_r($json_data);exit;
        
        $client = new Client();
        //先调用支付通知接口
        if($this->order->push_pay == 0){
            $response = $client->request('POST', 'http://lp-service.tmctech.cn/v1/pushPayInfo', [
                        'headers' => ['Content-Type' => 'application/json'],
                        'body' => $json_data
                    ]);
            $res = json_decode($response->getBody()->getContents(), true);
            if($res['results']['code'] == 200){
                // 通过事务执行 sql
                \DB::transaction(function() {
                    $this->order->update(['push_pay' => 1]);
                });
               //推送到唐山恒业系统
               $response = $client->request('POST', 'https://api.hihgo.com/channels/push_order', [
                   'headers' => ['Content-Type' => 'application/json'],
                   'json' => [
                       'cid' => '6c84c92c50cb4cfc',
                       'push_data' => $json_data
                   ]
               ]);
            }else{
                // 通过事务执行 sql
                \DB::transaction(function() {
                    $this->order->update(['customs_status' => Order::CUSTOMS_STATUS_FAILED, 'customs_data' => $res['results']['msg']]);
                });
                
                //$this->order->update(['customs_data' => $res['results']['msg']]);
            }
        }else{
            //推送到唐山恒业系统
            $response = $client->request('POST', 'https://api.hihgo.com/channels/push_order', [
                'headers' => ['Content-Type' => 'application/json'],
                'json' => [
                    'cid' => '6c84c92c50cb4cfc',
                    'push_data' => $json_data
                ]
            ]);
        }
        
        // 创建推送日志记录
        $item = new OrderCustomsLog([
            'type' => $this->order->type,
            'order_id' => $this->order->id,
            'order_no' => $this->order->no,
            'push_data' => $json_data,
            'synced' => 0,
            'shop_id' => $this->order->shop_id,
        ]);
        $item->save();
        
    }

    public function buildOrder($order){
//        $address = UserAddress::find ($order->address['id']);
//        $identity = UserIdentity::find ($order->identity['id']);
        if ($order->parent_id){
            $parent_order = Order::find($order->parent_id);
            $customs_pay = CustomsPay::where(['order_no'=>$parent_order->no])->first();
        }else{
            $customs_pay = CustomsPay::where(['order_no'=>$order->no])->first();
        }
        // 交易时间
        $payment_time = date("YmdHis", $order->paid_at->timestamp);
        if ($customs_pay && $pay_data = json_decode($customs_pay->pay_data, true)){
            if (!empty($pay_data['payExchangeInfoHead']['tradingTime'])){
                $payment_time = $pay_data['payExchangeInfoHead']['tradingTime'];
            }
        }
        // 支付流水
        $payment_no = $order->payment_no; // 支付流水
        if ($order->payment_method == 'allinpay' && !empty($order->pay_notify_data['trxid'])){
            $payment_no = $order->pay_notify_data['trxid'];
        }
        $data['logisticsType'] = $order->warehouse->express->code; // 物流类型
        $data['logisticsNo'] = '123456789'; // 物流单号
        $data['orderNo'] = !empty($order->suffix) ? $order->no.'_'.$order->suffix : $order->no; // 订单编号
        $data['orignOrderNo'] = $order->no; // 订单编号
        $data['mainPaymentFlow'] = $payment_no; // 主支付流水
        $data['paymentFlow'] = !empty($order->parent_id) ? $payment_no.'0'.$order->split_number : $payment_no; // 拆单支付流水
        $data['buyerName'] = $order->identity['real_name']; // 购买人姓名
        $data['buyerPhone'] = $order->address['contact_phone']; // 购买人电话
        $data['buyerIdType'] = '1'; // 购买人证件类型
        $data['buyerIdNo'] = $order->identity['idcard_no']; // 证件号码
        $data['consigneeprov'] = $order->address['province']; // 收货人省份
        $data['consigneecity'] = $order->address['city']; // 收货人市
        $data['consigneedistrict'] = $order->address['district']; // 收货人县
        $data['consigneezip'] = $order->address['zip']; // 收货人县
        $data['consigneeName'] = $order->address['contact_name']; // 收货人姓名
        $data['consigneePhone'] = $order->address['contact_phone']; // 收货人电
        $data['consigneeAddress'] = $order->address['address']; // 收货人地址

        $data['senderProvince'] = $order->warehouse->sender_province; // 发货人省份
        $data['senderCity'] = $order->warehouse->sender_city; // 发货人市
        $data['senderDistrict'] = $order->warehouse->sender_district; // 发货人县
        $data['senderAddress'] = $order->warehouse->sender_address; // 发货人详细地址
        $data['senderMobile'] = $order->warehouse->sender_mobile; // 发货人电话
        $data['senderTelephone'] = $order->warehouse->sender_telephone; // 发货人手机号

        $data['totalAmount'] = $order->total_amount; // 总金额
        $data['taxAmount'] = $order->tax_amount; // 税费
        $data['netWeight'] = $this->calculateNetWeight($order); // 净重
        $data['status'] = '3000'; //统一状态
        $data['wCode'] = $order->warehouse->code; // 仓库code
        $data['paymentType'] = $order->payment_method; // 支付方式
        $data['paymentTime'] = $payment_time; // 支付时间
        $data['isSplit'] = !empty($order->parent_id) ? '1' : '0'; // 是否拆单
//        $data['coupon_amount'] = (string)round($order->coupon_amount, 2); // 优惠券金额
//        $data['discount_amount'] = (string)round($order->discount_amount, 2); // 折扣金额
//        $data['freight'] = (string)round($order->freight, 2); // 运费
        $data['discount'] = (string)round($order->coupon_amount + $order->discount_amount, 2); // 优惠金额
        $data['freight'] = (string)round($order->freight, 2); // 运费
        return $data;
    }
    public function buildOrderItems($order){
        $items = $order->items;
        foreach ($items as $k=>$item) {
            $sku = $item->productSku;
            //$product = $item->product;
            $data[$k]['gnum'] = $sku->no; // 商品序号
            $data[$k]['itemNo'] = $sku->item_no; // 企业商品货号
            $data[$k]['itemName'] = $sku->item_name; // 企业商品名
            $data[$k]['itemRecordNo'] = $sku->item_record_no; // 报税进口
            $data[$k]['gmodel'] = $sku->gmodel; // 商品规格型号
            $data[$k]['country'] = $sku->country; // 原产国
            //$data[$k]['qty'] = (string)($sku->qty * $item->amount); // 数量
            $data[$k]['qty'] = (string)$item->qty * $item->amount; // 数量
            $data[$k]['qty1'] = (string)round($item->qty * $sku->qty1 * $item->amount, 5); // 法定数量
            $data[$k]['qty2'] = !empty($sku->qty2 * $item->amount) ? (string)($item->qty * $sku->qty2 * $item->amount) : ''; // 第二数量
            $data[$k]['unit'] = $sku->unit; // 计量单位
            $data[$k]['unit1'] = $sku->unit1; // 法定计量单位
            $data[$k]['unit2'] = !empty($sku->unit2) ? $sku->unit2 : ''; // 第二计量单位
            //$data[$k]['price'] = (string)round(($item->price - $item->coupon) / $sku->qty, 2); // 成交单价
            $data[$k]['price'] = (string)round(($item->price - $item->coupon) / $item->qty, 2); // 成交单价
            $data[$k]['gCode'] = $sku->gcode; // 商品编码
            $data[$k]['gName'] = $sku->title; // 商品名称
            $data[$k]['num'] = $item->amount; // 数量
        }
        return $data;
    }
    
    public function calculateNetWeight($order){
        $items = $order->items;
        $net_weight = 0;
        foreach ($items as $k=>$item) {
            $sku = $item->productSku;
            $net_weight += $item->amount * $sku->net_weight * $sku->qty;
        }
        return (string)round($net_weight, 5);
    }
}
