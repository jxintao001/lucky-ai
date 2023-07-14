<?php

namespace App\Services;

use App\Exceptions\InvalidRequestException;
use App\Handlers\RabbitMqPublishHandler;
use App\Models\CustomsPay;
use App\Models\Order;
use App\Models\OrderCustomsLog;
use App\Models\UserAddress;
use App\Models\UserIdentity;
use Carbon\Carbon;
use Dingo\Api\Exception\ResourceException;
use Illuminate\Support\Facades\Log;

class CustomsService
{
    protected $rabbitMqPublishHandler;

    public function __construct(RabbitMqPublishHandler $rabbitMqPublishHandler)
    {
        $this->rabbitMqPublishHandler = $rabbitMqPublishHandler;
    }

    public function buildData($order){
        $data = $this->buildOrder($order);
        $items = $this->buildOrderItems($order);
        $data['orderList'] = $items;
        return $data;
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
        //$data['consigneedistrict'] = $order->address['district']; // 收货人县
        $data['consigneedistrict'] = $order->address['zip']; // 收货人县  //清关接口consigneedistrict非必填，用邮编填写可以正确
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
            $data[$k]['qty'] = (string)($item->qty * $item->amount); // 数量
            $data[$k]['qty1'] = (string)round($item->qty * $sku->qty1 * $item->amount, 5); // 法定数量
            //$data[$k]['qty2'] = !empty($sku->qty2 * $item->amount) ? (string)($sku->qty2 * $item->amount) : ''; // 第二数量
            $data[$k]['qty2'] = !empty($sku->qty2) && !empty($item->amount) ? (string)($item->qty * $sku->qty2 * $item->amount) : ''; // 第二数量
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

    public function clearance(Order $order)
    {
        // 构建数据
        try {
            $data = $this->buildData($order);
        } catch (\Exception $e) {
            Log::error($e->getMessage(), $order->toArray());
            throw new ResourceException('清关数据推送失败');
        }
        // 创建推送日志记录
        $item = new OrderCustomsLog([
            'type' => $order->type,
            'order_id' => $order->id,
            'order_no' => $order->no,
            'push_data' => json_encode($data, JSON_UNESCAPED_UNICODE|JSON_UNESCAPED_SLASHES),
            'synced' => 0,
            'shop_id' => $order->shop_id,
        ]);
        $item->save();
        $this->rabbitMqPublishHandler->publish($data);
    }

    public function cancel(Order $order)
    {
        // 构建数据
        try {
            $data = $this->buildData($order);
        } catch (\Exception $e) {
            Log::error($e->getMessage(), $order->toArray());
            throw new ResourceException('海关撤单数据推送失败');
        }
        $this->rabbitMqPublishHandler->publish($data, 'order_cancel', 'order_cancel_exchange', 'order_cancel_key');
    }

    public function refund(Order $order)
    {
        // 构建数据
        try {
            $data = $this->buildData($order);
        } catch (\Exception $e) {
            Log::error($e->getMessage(), $order->toArray());
            throw new ResourceException('海关退款数据推送失败');
        }
        $this->rabbitMqPublishHandler->publish($data, 'order_refund', 'order_refund_exchange', 'order_refund_key');
    }

    public function buildPayData($order){
        $data = $this->buildPayOrder($order);
        return $data;
    }

    public function buildPayOrder($order){
        $data = [];
        $initalRequest = [];
        foreach ($order->pay_data as $k=>$item){
            $initalRequest[] = $k.'='.$item;
        }
        if ($order->payment_method == 'wechat'){
            $payExchangeInfoHead['initalRequest'] = 'https://api.mch.weixin.qq.com/pay/unifiedorder?'.implode('&',$initalRequest);
        }elseif ($order->payment_method == 'allinpay'){
            $payExchangeInfoHead['initalRequest'] = 'https://vsp.allinpay.com/apiweb/unitorder/pay?'.implode('&',$initalRequest);
        }
        $payExchangeInfoHead['initalResponse'] = '';//$order->pay_notify_data;
        $payExchangeInfoHead['payTransactionId'] = $order->payment_no;
        $payExchangeInfoHead['totalAmount'] = $order->total_amount;
        $payExchangeInfoHead['currency'] = '142';
        $payExchangeInfoHead['verDept'] = '3';
        $payExchangeInfoHead['payType'] = '1';
        $payExchangeInfoHead['tradingTime'] = date('YmdHis',$order->paid_at->timestamp);
        $payExchangeInfoHead['note'] = $order->pay_data['body'];

        $goodsInfo = $this->buildPayOrderGoods($order);
        $payExchangeInfoLists['orderNo'] = $order->no;
        $payExchangeInfoLists['goodsInfo'] = $goodsInfo;

        $data['type'] = $order->payment_method;
        $data['payExchangeInfoHead'] = $payExchangeInfoHead;
        $data['payExchangeInfoLists'] = $payExchangeInfoLists;
        return $data;
    }
    public function buildPayOrderGoods($order){
        $items = $order->items;
        foreach ($items as $k=>$item) {
            $sku = $item->productSku;
            $product = $item->product;
            $data[$k]['gname'] = $sku->title; // 商品名称
            $data[$k]['itemLink'] = 'http://www.hihgo.com/index.php?c=goods&a=index&id='.$product->id.'&sku_id='.$sku->id; // 商品ID
        }
        return $data;
    }

    public function payClearance(Order $order)
    {
        // 构建数据
        try {
            $data = $this->buildPayData($order);
        } catch (\Exception $e) {
            Log::error($e->getMessage(), $order->toArray());
            throw new ResourceException('清关支付数据构建失败');
        }
        // 创建推送日志记录
        if ($item = CustomsPay::where('order_no', $order->no)->first()) {
            $item->pay_data = json_encode($data, JSON_UNESCAPED_UNICODE|JSON_UNESCAPED_SLASHES);
            $item->save();
        } else {
            $item = new CustomsPay([
                'order_no' => $order->no,
                'pay_data' => json_encode($data, JSON_UNESCAPED_UNICODE|JSON_UNESCAPED_SLASHES),
            ]);
            $item->save();
        }
    }

    public function clearanceStatus(Order $order, $status, $remark='')
    {

        switch ($status)
        {
            // 清关中
            case '2000':
                $customs_status = Order::CUSTOMS_STATUS_PROCESSING;
                break;
            // 支付申报失败
            case '1100':
                $customs_status = Order::CUSTOMS_STATUS_FAILED;
                break;
            // 物流单获取失败
            case '1300':
                $customs_status = Order::CUSTOMS_STATUS_FAILED;
                break;
            // 清关失败
            case '3000':
                $customs_status = Order::CUSTOMS_STATUS_FAILED;
                break;
            // 订单申报失败
            case '4000':
                $customs_status = Order::CUSTOMS_STATUS_FAILED;
                break;
            // 清关成功
            case '5000':
                $customs_status = Order::CUSTOMS_STATUS_SUCCESS;
                break;
            // 清关撤销中
            case '7001':
                $customs_status = Order::CUSTOMS_STATUS_CANCEL_PROCESSING;
                break;
            // 清关撤销失败
            case '7002':
                $customs_status = Order::CUSTOMS_STATUS_CANCEL_FAILED;
                break;
            // 清关撤销成功
            case '7000':
                $customs_status = Order::CUSTOMS_STATUS_CANCEL_SUCCESS;
                break;
            default:
                throw new ResourceException('状态码异常');
        }
        // 已经清关成功，直接返回
        if ($order->customs_status === Order::CUSTOMS_STATUS_REFUND_SUCCESS){
            return false;
        }
        // 更新订单清关状态
        $order->customs_status = $customs_status;
        $order->customs_data = $remark;
        $order->save();
        return true;

    }

    public function pushRabbitMQ($data)
    {

        return $order;
    }
}
