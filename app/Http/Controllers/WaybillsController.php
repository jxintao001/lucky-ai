<?php

namespace App\Http\Controllers;


use App\Models\CustomsNoStatus;
use App\Models\Order;
use App\Transformers\OrderTransformer;
use Carbon\Carbon;
use Dingo\Api\Routing\Helpers;
use League\Fractal\Manager as FractalManager;
use League\Fractal\Resource\Item;
use Milon\Barcode\DNS1D;

class WaybillsController extends Controller
{
    use Helpers;
    public function __construct(FractalManager $fractal) {
        parent::__construct();
        $this->fractal = $fractal;
    }
    public function show($id)
    {
        $ret_data = [];
        $order = Order::findOrfail($id);
        $order_no = !empty($order->suffix) ? $order->no.'_'.$order->suffix : $order->no;
        $customsNoStatus = CustomsNoStatus::where('order_no', $order_no)->first();
        $sender['province'] = $order->warehouse->sender_province;
        $sender['city'] = $order->warehouse->sender_city;
        $sender['district'] = $order->warehouse->sender_district;
        $sender['address'] = $order->warehouse->sender_address;
        $sender['sender_name'] = $order->warehouse->sender_name;
        $sender['mobile'] = $order->warehouse->sender_mobile;
        $sender['phone'] = $order->warehouse->sender_telephone;
        // 商品描述
        $items = [];
        foreach ($order->items as $item){
            $items[] = $item->productSku->title.' * '.$item->amount;
        }
        $ret_data['id'] = $order->id;
        $ret_data['no'] = $order->no;
        $ret_data['waybill_no'] = !empty($customsNoStatus->logistics_no) ? $customsNoStatus->logistics_no : '';
        $ret_data['marker'] = !empty($customsNoStatus->big_pen) ? $customsNoStatus->big_pen : '';
        $ret_data['exchange_office'] = '';
        $ret_data['bar_code'] = !empty($customsNoStatus->logistics_img) ? $customsNoStatus->logistics_img : '';
        $ret_data['sender'] = $sender;
        $ret_data['receiver'] = $order->address;
        $ret_data['order_items'] = $items;
        // 更新物流状态
        if (!empty($ret_data['waybill_no']) && $order->ship_status === Order::SHIP_STATUS_PENDING){
            // 将订单发货状态改为已发货，并存入物流信息
            $ship_data['express_company'] = $order->warehouse->express->name;
            $ship_data['express_no'] = $ret_data['waybill_no'];
            $order->update([
                'ship_status' => Order::SHIP_STATUS_PRINTED_PENDING,
                // 我们在 Order 模型的 $casts 属性里指明了 ship_data 是一个数组
                // 因此这里可以直接把数组传过去
                'ship_data' => $ship_data,
                'delivered_at' => Carbon::now(),
                'waybill_print' => $order->waybill_print + 1,
            ]);
        }
        return $ret_data;
    }

    public function sorting($id)
    {
        $order = Order::findOrFail($id);
        $orderTransformer = new OrderTransformer();
        $orderTransformer->setDefaultIncludes(['items']);
        $order = $this->fractal->createData(new Item ($order, $orderTransformer))->toArray();
        foreach ($order['data']['items']['data'] as &$item){
            $item['productSku']['data']['bar_code'] = 'data:image/png;base64,' . DNS1D::getBarcodePNG($item['productSku']['data']['no'], "C39",1,33);
        }
        return $order;
    }

    public function markPrint($id)
    {
        $order = Order::findOrfail($id);
        $order->waybill_print = $order->waybill_print + 1;
        $order->save();
    }


}