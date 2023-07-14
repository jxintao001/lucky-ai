<?php

namespace App\Http\Controllers;

use App\Http\Requests\OrderExchangeRequest;
use App\Models\Order;
use App\Models\User;
use App\Models\UserAddress;
use App\Services\OrderService;
use App\Transformers\OrderExchangeTransformer;
use Dingo\Api\Exception\ResourceException;
use Dingo\Api\Routing\Helpers;
use Illuminate\Http\Request;
use League\Fractal\Manager as FractalManager;

class ExchangeOrdersController extends Controller
{
    use Helpers;

    public function __construct(FractalManager $fractal)
    {
        parent::__construct();
        $this->fractal = $fractal;
    }

    public function index(Request $request)
    {
        $type = !empty($request['type']) ? strval($request['type']) : 'normal';
        $status = !empty($request['status']) ? strval($request['status']) : 'normal';
        //DB::connection()->enableQueryLog(); // 开启查询日志
        $orders = Order::query()->where('user_id', auth('api')->id())
            ->where('shop_id', auth('api')->user()->shop_id)
            ->where('type', Order::TYPE_EXCHANGE)
            ->where('split', 0);
        if ($status) {
            if ($status == 'unpaid') { // 待支付
                $orders->whereNull('paid_at')->where('closed', '0');
            } elseif ($status == 'paid') { // 已支付
                $orders->whereNotNull('paid_at')->where('closed', '0');
            } elseif ($status == 'unshipped') { // 待发货
                $orders->whereNotNull('paid_at')->where('closed', '0')->where('ship_status', 'pending');
            } elseif ($status == 'unreceived') { // 待收货
                $orders->whereNotNull('paid_at')->where('closed', '0')->where('ship_status', 'delivered');
            } elseif ($status == 'received') { // 已收货
                $orders->whereNotNull('paid_at')->where('closed', '0')->where('ship_status', 'received');
            } elseif ($status == 'refund') { // 退换货
                $orders->whereNotNull('paid_at')->where('closed', '0')->where('refund_status', '<>', 'pending');
            } elseif ($status == 'closed') { // 已关闭
                $orders->where('closed', '1');
            }
        }
        if ($type) {
            if ($type == 'virtual') {
                $orders->where('is_virtual', 1);
            } elseif ($type == 'pick_up') {
                $orders->where('delivery_method', $type);
            } elseif ($type == 'express') {
                $orders->where('delivery_method', $type);
            }
        }
        $orders = $orders->has('items')->orderBy('created_at', 'desc')
            ->paginate(per_page(20));
        //dd(DB::getQueryLog());
        $orderTransformer = new OrderExchangeTransformer();
        $orderTransformer->setDefaultIncludes(['items', 'cards', 'stores']);
        return $this->response()->paginator($orders, $orderTransformer);
    }

    public function show($id)
    {
        $order = Order::findOrFail($id);
        $this->authorize('own', $order);
        $orderTransformer = new OrderExchangeTransformer();
        $orderTransformer->setDefaultIncludes(['items', 'cards', 'stores']);
        return $this->response()->item($order, $orderTransformer);
    }

    public function store(OrderExchangeRequest $request, OrderService $orderService)
    {
        $user = User::find(auth('api')->id());
        $address = null;
        if ($request->input('address_id')) {
            $address = UserAddress::find($request->input('address_id'));
        }
        $order = $orderService->exchange($user, $request->input('items'), $address, $request->input('delivery_method'), $request->input('remark'));
        $order = Order::findOrFail($order->id);
        $orderTransformer = new OrderExchangeTransformer();
        $orderTransformer->setDefaultIncludes(['items', 'cards', 'stores']);
        return $this->response()->item($order, $orderTransformer);
    }

    public function destroy($id)
    {
        // 订单信息
        $order = Order::findOrFail($id);
        // 校验权限
        $this->authorize('own', $order);
        // 判断订单的发货状态是否未付款
        if (!$order->closed) {
            throw new ResourceException('已关闭的订单才能删除');
        }
        // 删除订单
        $order->delete();
        return $this->response()->noContent();
    }

    public function received(Order $order, Request $request)
    {
        // 校验权限
        $this->authorize('own', $order);
        // 判断订单的发货状态是否为已发货
        if ($order->ship_status !== Order::SHIP_STATUS_DELIVERED) {
            return $this->errorBadRequest('发货状态不正确');
        }

        // 更新发货状态为已收到
        $order->update(['ship_status' => Order::SHIP_STATUS_RECEIVED]);

        return $this->response()->noContent();
    }


}
