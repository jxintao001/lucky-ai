<?php

namespace App\Http\Controllers;

use App\Http\Requests\ConfirmOrderRequest;
use App\Http\Requests\GiftPackageRequest;
use App\Models\CouponCode;
use App\Models\GiftPackage;
use App\Models\GiftPackageReceive;
use App\Models\GiftPackageTemplate;
use App\Models\Order;
use App\Models\User;
use App\Services\GiftPackageService;
use App\Services\SplitOrderService;
use App\Transformers\GiftPackageReceiveItemTransformer;
use App\Transformers\GiftPackageTransformer;
use App\Transformers\OrderTransformer;
use Dingo\Api\Exception\ResourceException;
use Dingo\Api\Routing\Helpers;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use League\Fractal\Manager as FractalManager;

class GiftPackagesController extends Controller
{
    use Helpers;

    public function __construct(FractalManager $fractal)
    {
        parent::__construct();
        $this->fractal = $fractal;
    }

    public function confirm(ConfirmOrderRequest $request, SplitOrderService $splitOrderService)
    {
        $user = User::find(auth('api')->id());
        $ret_data['totalAmount'] = 0;
        return $ret_data;
    }

    public function index(Request $request)
    {
        $type = !empty($request['type']) ? strval($request['type']) : 'normal';
        $status = !empty($request['status']) ? strval($request['status']) : 'normal';
        //DB::connection()->enableQueryLog(); // 开启查询日志
        $orders = Order::query()->where('user_id', auth('api')->id())
            ->where('shop_id', auth('api')->user()->shop_id)
            ->where('split', 0);
        if ($type) {
            $orders->where('type', $type);
        }
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
        $orders = $orders->has('items')->orderBy('created_at', 'desc')
            ->paginate(per_page(100));
        //dd(DB::getQueryLog());
        $orderTransformer = new OrderTransformer();
        $orderTransformer->setDefaultIncludes(['items']);
        return $this->response()->paginator($orders, $orderTransformer);
    }

    public function show($no)
    {
        if (!$no) {
            throw new ResourceException('礼盒编号不存在');
        }
        $gift_package = GiftPackage::where('no', $no)->first();
        if (!$gift_package) {
            throw new ResourceException('礼盒不存在');
        }
        // 当前用户是否领取过
        if (auth('api')->id()) {
            $received = GiftPackageReceive::where([
                'user_id'         => auth('api')->id(),
                'gift_package_id' => $gift_package->id,
            ])->exists();
            $gift_package->receive_status = $received ? 1 : 0;
        }
        $transformer = new GiftPackageTransformer();
        //$transformer->setDefaultIncludes(['items']);
        return $this->response()->item($gift_package, $transformer);
    }

    public function store(GiftPackageRequest $request, GiftPackageService $packageService)
    {
        if ($request['template_id']) {
            $template = GiftPackageTemplate::find($request['template_id']);
            if (!$template) {
                throw new ResourceException('模版不存在');
            }
        }
        $user = User::find(auth('api')->id());
        $package = $packageService->store($user, $request);
        $package = GiftPackage::findOrFail($package->id);
        $packageTransformer = new GiftPackageTransformer();
        $packageTransformer->setDefaultIncludes(['items', 'template']);
        return $this->response()->item($package, $packageTransformer);
    }

    public function receive($no, Request $request, GiftPackageService $packageService)
    {
        if (!$no) {
            throw new ResourceException('礼盒编号不存在');
        }
        $user = User::find(auth('api')->id());
        $gift_package = GiftPackage::where('no', $no)->where('shop_id', $user->shop_id)->first();
        if (!$gift_package) {
            throw new ResourceException('礼盒不存在');
        }
        $gifts = $packageService->receive($user, $gift_package, $request['answer']);
        $transformer = new GiftPackageReceiveItemTransformer();
        //$transformer->setDefaultIncludes(['items']);
        return $this->response()->collection($gifts, $transformer);
    }

    public function closed($no, GiftPackageService $giftPackageService)
    {
        if (!$no) {
            throw new ResourceException('礼包编号不存在');
        }
        $user = User::find(auth('api')->id());
        $gift_package = GiftPackage::where('no', $no)->where('user_id', $user->id)->where('shop_id', $user->shop_id)->first();
        if (!$gift_package) {
            throw new ResourceException('礼包不存在');
        }
        // 是否关闭
        if ($gift_package->closed) {
            throw new ResourceException('礼包已经关闭过了');
        }
        // 判断礼包状态
        if ($gift_package->status === GiftPackage::STATUS_EXPIRED) {
            throw new ResourceException('礼包已经过期');
        }
        if ($gift_package->status === GiftPackage::STATUS_FINISH) {
            throw new ResourceException('礼包已经领完');
        }
        // 通过事务执行 sql
        DB::transaction(function () use ($gift_package, $giftPackageService) {
            $giftPackageService->returnGiftPackage($gift_package);
        });
        return $this->response()->noContent();
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


}
