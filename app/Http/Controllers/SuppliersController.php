<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\SupplierStore;
use App\Services\OrderService;
use Dingo\Api\Exception\ResourceException;
use Illuminate\Support\Facades\Cache;
use Ramsey\Uuid\Uuid;
use Symfony\Component\HttpKernel\Exception\UnauthorizedHttpException;

class SuppliersController extends Controller
{

    public function login()
    {
        if (!request('username') || !request('password')) {
            throw new ResourceException('用户名密码不能为空');
        }
        $supplier_store = SupplierStore::query()->where('username', request('username'))
            ->where('password', request('password'))
            ->first();
        if (!$supplier_store) {
            throw new ResourceException('用户名或密码错误');
        }
        $token = Uuid::uuid4()->getHex();
        if (!$token || strlen($token) != 32) {
            throw new ResourceException('登录异常');
        }
        $ret = Cache::add(SupplierStore::$token_prefix . $token, $supplier_store->toArray(), SupplierStore::$token_expiration);
        if (!$ret) {
            throw new ResourceException('登录异常');
        }
        $ret_data['data'] = ['token' => $token];
        return $ret_data;
    }

    public function writeOff(OrderService $orderService)
    {
        if (!request('supplier_token') || !request('uuid')) {
            throw new ResourceException('请求参数错误');
        }
        $auth = supplierAuth(request('supplier_token'));
        if (!$auth) {
            throw new UnauthorizedHttpException('', '无效token');
        }
        $supplier_store = SupplierStore::find($auth['id']);
        if (!$supplier_store) {
            throw new UnauthorizedHttpException('', '账号异常');
        }
        $order = Order::where('uuid', request('uuid'))->first();
        if (!$order) {
            throw new ResourceException('订单不存在');
        }
        if ($order->type !== Order::TYPE_EXCHANGE) {
            throw new ResourceException('订单类型错误');
        }
        if ($order->closed) {
            throw new ResourceException('订单已关闭');
        }
        if ($order->received_at) {
            throw new ResourceException('订单已核销');
        }
        $orderService->writeOff($order, $supplier_store);
        return $this->response->created();
    }


}