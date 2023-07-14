<?php

namespace App\Http\Controllers;

use App\Http\Requests\ApplyRefundRequest;
use App\Http\Requests\BargainOrderRequest;
use App\Http\Requests\ConfirmGroupOrderRequest;
use App\Http\Requests\ConfirmOrderRequest;
use App\Http\Requests\GroupOrderRequest;
use App\Http\Requests\OrderRequest;
use App\Jobs\Customs;
use App\Jobs\CustomsCancel;
use App\Jobs\CustomsHyie;
use App\Jobs\CustomsRefund;
use App\Models\Bargain;
use App\Models\CouponCode;
use App\Models\CustomsPay;
use App\Models\FoodStampSet;
use App\Models\Group;
use App\Models\Level;
use App\Models\FreightRule;
use App\Models\Order;
use App\Models\ProductSku;
use App\Models\User;
use App\Models\UserAddress;
use App\Models\UserIdentity;
use App\Models\UserIntegral;
use App\Services\CustomsService;
use App\Services\OrderService;
use App\Services\SplitOrderService;
use App\Services\UserIntegralService;
use App\Transformers\BargainTransformer;
use App\Transformers\OrderTransformer;
use App\Transformers\ProductSkuTransformer;
use App\Transformers\UserCouponCodeTransformer;
use Carbon\Carbon;
use Dingo\Api\Exception\ResourceException;
use Dingo\Api\Routing\Helpers;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use League\Fractal\Manager as FractalManager;
use League\Fractal\Resource\Collection;
use League\Fractal\Resource\Item;

class OrdersController extends Controller
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
        $ret_data['totalWeight'] = 0;
        $ret_data['freight'] = 0;
        $ret_data['totalAmount'] = 0;
        // 遍历用户提交的 SKU
        $product_ids = [];
        $items = [];
        foreach ($request->input('items') as $k => $data) {
            $sku = ProductSku::with('product')->find($data['sku_id']);
            $items[$k] = $sku->toArray();
            $items[$k]['amount'] = $data['amount'];
            $sku_price = $sku->tax_price;
            //等级用户会员价
            if ($user->level == 1) {
                $sku_price = $sku->member_price;
                $items[$k]['price'] = $sku->member_price;
                $items[$k]['tax_rate'] = $sku->member_price;
            }
            if ($user->level == 2) {
                $sku_price = $sku->club_price;
                $items[$k]['price'] = $sku->club_price;
                $items[$k]['tax_rate'] = $sku->club_price;
            }
            //$ret_data['totalAmount'] += $sku->price * $data['amount'];
            $ret_data['totalWeight'] += sprintf("%.2f", $sku->weight * $data['amount'] * $sku->qty);
            $ret_data['totalAmount'] += $sku_price * $data['amount'];
            $transformer = new ProductSkuTransformer();
            $transformer->setDefaultIncludes(['product']);
            $sku = $this->fractal->createData(new Item ($sku, $transformer))->toArray();
            $sku['amount'] = $data['amount'];
            $ret_data['product_skus'][] = $sku;
            $product_ids[] = $sku['data']['product']['data']['id'];
        }
        
        $ret_data['totalAmount'] = sprintf("%.2f", $ret_data['totalAmount']);
        //DB::connection()->enableQueryLog(); // 开启查询日志
        // 获取可用优惠券
        $coupons = $user->couponCodes()
            ->where(function ($query) use ($product_ids) {
                $query->where(function ($query) use ($product_ids) {
                    $query->where('coupon_codes.use_type', '<>', CouponCode::USE_TYPE_ASSIGN_PRODUCT);
                })
                    ->orWhere(function ($query) use ($product_ids) {
                        $query->where('coupon_codes.use_type', CouponCode::USE_TYPE_ASSIGN_PRODUCT)
                            ->whereIn('coupon_codes.target_id', $product_ids);
                    });
            })->where('user_coupon_codes.used', 0)
            ->where('coupon_codes.min_amount', '<=', $ret_data['totalAmount'])
            ->where('coupon_codes.not_after', '>=', Carbon::now())
            ->orderBy('coupon_codes.value', 'desc')
            ->take(20)->get();
        //print_r(DB::getQueryLog());exit();
        $coupons = $this->fractal->createData(new Collection($coupons, new UserCouponCodeTransformer()))->toArray();
        //$ret_data['freight'] = $splitOrderService->getTotalFreight($items);
        $ret_data['freight'] = sprintf("%.2f", $splitOrderService->getTotalFreight($items, $ret_data['totalWeight'], $request->input('address_id')));
        $ret_data['taxAmount'] = $splitOrderService->getTotalTaxAmount($items);
        //$ret_data['productAmount'] = sprintf("%.2f", $ret_data['totalAmount'] + $ret_data['taxAmount']);
        $ret_data['productAmount'] = sprintf("%.2f", $ret_data['totalAmount']);
        $level = Level::where('level', $user->level)->first();
        $ret_data['discountAmount'] = 0;
        if ($level) {
            $ret_data['totalAmount'] = $level->getAdjustedPrice($ret_data['totalAmount']);
            $ret_data['discountAmount'] = sprintf("%.2f", $ret_data['productAmount'] - $ret_data['totalAmount']);
        }
        $ret_data['integral'] = $user->integral ?? 0;
        $ret_data['food_stamp'] = $user->food_stamp ?? 0;
        //$ret_data['totalAmount'] = sprintf("%.2f", $ret_data['totalAmount'] + $ret_data['freight'] + $ret_data['taxAmount']);
        $ret_data['totalAmount'] = sprintf("%.2f", $ret_data['totalAmount'] + $ret_data['freight']);
        if ($coupons) $ret_data['coupons'] = $coupons;
        
        return $ret_data;
    }

    public function confirmGroups(ConfirmGroupOrderRequest $request, SplitOrderService $splitOrderService)
    {
        $items = [];
        $amount = $request->input('amount');
        $user = User::find(auth('api')->id());
        $sku = ProductSku::with('product')->find($request->input('sku_id'));
        $items[0] = $sku->toArray();
        $items[0]['amount'] = $amount;
        $ret_data['totalAmount'] = $sku->product->group->price * $amount;
        $transformer = new ProductSkuTransformer();
        $transformer->setDefaultIncludes(['product', 'group']);
        $sku = $this->fractal->createData(new Item ($sku, $transformer))->toArray();
        $sku['amount'] = $amount;
        $ret_data['product_skus'][] = $sku;
        $product_ids[] = $sku['data']['product']['data']['id'];
        //DB::connection()->enableQueryLog(); // 开启查询日志
        $ret_data['totalAmount'] = sprintf("%.2f", $ret_data['totalAmount']);
        // 获取可用优惠券
        $coupons = $user->couponCodes()
            ->where(function ($query) use ($product_ids) {
                $query->where(function ($query) use ($product_ids) {
                    $query->where('coupon_codes.use_type', '<>', CouponCode::USE_TYPE_ASSIGN_PRODUCT);
                })
                    ->orWhere(function ($query) use ($product_ids) {
                        $query->where('coupon_codes.use_type', CouponCode::USE_TYPE_ASSIGN_PRODUCT)
                            ->whereIn('coupon_codes.target_id', $product_ids);
                    });
            })->where('user_coupon_codes.used', 0)
            ->where('coupon_codes.min_amount', '<=', $ret_data['totalAmount'])
            ->where('coupon_codes.not_after', '>=', Carbon::now())
            ->orderBy('coupon_codes.value', 'desc')
            ->take(20)->get();
        //print_r(DB::getQueryLog());exit();
        $coupons = $this->fractal->createData(new Collection($coupons, new UserCouponCodeTransformer()))->toArray();
        $ret_data['freight'] = $splitOrderService->getTotalFreight($items);
        $ret_data['taxAmount'] = $splitOrderService->getTotalTaxAmount($items);
        $ret_data['productAmount'] = sprintf("%.2f", $ret_data['totalAmount']);
        $level = Level::where('level', $user->level)->first();
        $ret_data['discountAmount'] = 0;
        if ($level) {
            $ret_data['totalAmount'] = $level->getAdjustedPrice($ret_data['totalAmount']);
            $ret_data['discountAmount'] = sprintf("%.2f", $ret_data['productAmount'] - $ret_data['totalAmount']);
        }
        //$ret_data['totalAmount'] = sprintf("%.2f", $ret_data['totalAmount'] + $ret_data['freight'] + $ret_data['taxAmount']);
        $ret_data['totalAmount'] = sprintf("%.2f", $ret_data['totalAmount'] + $ret_data['freight']);
        if ($coupons) $ret_data['coupons'] = $coupons;
        return $ret_data;
    }

    public function confirmBargains(Request $request, SplitOrderService $splitOrderService)
    {
        $items = [];
        $bargain = Bargain::findOrFail($request->input('bargain_id'));
        $this->authorize('own', $bargain);
        $transformer = new BargainTransformer();
        //$transformer->setDefaultIncludes(['items']);
        $bargain = $this->fractal->createData(new Item($bargain, $transformer))->toArray();
        $ret_data['totalAmount'] = $bargain['data']['current_price'];
        //$ret_data['bargain'] = $bargain;
        $sku = ProductSku::with('product')->find($bargain['data']['sku_id']);
        $items[0] = $sku->toArray();
        $items[0]['amount'] = 1;
        $transformer = new ProductSkuTransformer();
        $transformer->setDefaultIncludes(['product', 'bargain']);
        $sku = $this->fractal->createData(new Item ($sku, $transformer))->toArray();
        $sku['amount'] = '1';
        $ret_data['product_skus'][] = $sku;
        $user = User::find(auth('api')->id());
        $product_ids[] = $sku['data']['product']['data']['id'];
        $ret_data['totalAmount'] = sprintf("%.2f", $ret_data['totalAmount']);
        // 获取可用优惠券
        $coupons = $user->couponCodes()
            ->where(function ($query) use ($product_ids) {
                $query->where(function ($query) use ($product_ids) {
                    $query->where('coupon_codes.use_type', '<>', CouponCode::USE_TYPE_ASSIGN_PRODUCT);
                })
                    ->orWhere(function ($query) use ($product_ids) {
                        $query->where('coupon_codes.use_type', CouponCode::USE_TYPE_ASSIGN_PRODUCT)
                            ->whereIn('coupon_codes.target_id', $product_ids);
                    });
            })->where('user_coupon_codes.used', 0)
            ->where('coupon_codes.min_amount', '<=', $ret_data['totalAmount'])
            ->where('coupon_codes.not_after', '>=', Carbon::now())
            ->orderBy('coupon_codes.value', 'desc')
            ->take(20)->get();
        $coupons = $this->fractal->createData(new Collection($coupons, new UserCouponCodeTransformer()))->toArray();
        if ($coupons) $ret_data['coupons'] = $coupons;
        $ret_data['freight'] = $splitOrderService->getTotalFreight($items);
        $ret_data['taxAmount'] = $splitOrderService->getTotalTaxAmount($items);
        $ret_data['productAmount'] = sprintf("%.2f", $ret_data['totalAmount']);
        $level = Level::where('level', $user->level)->first();
        $ret_data['discountAmount'] = 0;
        if ($level) {
            $ret_data['totalAmount'] = $level->getAdjustedPrice($ret_data['totalAmount']);
            $ret_data['discountAmount'] = sprintf("%.2f", $ret_data['productAmount'] - $ret_data['totalAmount']);
        }
        //$ret_data['totalAmount'] = sprintf("%.2f", $ret_data['totalAmount'] + $ret_data['freight'] + $ret_data['taxAmount']);
        $ret_data['totalAmount'] = sprintf("%.2f", $ret_data['totalAmount'] + $ret_data['freight']);
        return $ret_data;
    }

    public function index(Request $request)
    {
        $type = !empty($request['type']) ? [$request['type']] : ['normal', 'gift', 'select', 'exchange', 'recharge'];
        $status = !empty($request['status']) ? strval($request['status']) : 'normal';
        //DB::connection()->enableQueryLog(); // 开启查询日志
        $orders = Order::query()->where('user_id', auth('api')->id())
            ->where('shop_id', auth('api')->user()->shop_id)
            ->where('split', 0);
        if ($type) {
            $orders->whereIn('type', $type);
        }
        if ($status) {
            if ($status == 'unpaid') { // 待支付
                $orders->whereNull('paid_at')->where('closed', '0');
            } elseif ($status == 'paid') { // 已支付
                $orders->whereNotNull('paid_at')->where('closed', '0');
            } elseif ($status == 'unshipped') { // 待发货
                $orders->whereNotNull('paid_at')->where('closed', '0')->where('ship_status', 'pending')->where('type', '<>', 'gift');
            } elseif ($status == 'unreceived') { // 待收货
                $orders->whereNotNull('paid_at')->where('closed', '0')->where('ship_status', 'delivered');
            } elseif ($status == 'received') { // 已收货
                $orders->whereNotNull('paid_at')->where('closed', '0')->where('ship_status', 'received');
            } elseif ($status == 'refund') { // 退换货
                $orders->whereNotNull('paid_at')->where('closed', '0')->where('refund_status', '<>', 'pending');
            } elseif ($status == 'closed') { // 已关闭
                $orders->where('closed', '1');
            } elseif ($status == 'comment') { // 待评价
                $orders->whereNotNull('paid_at')->where('closed', '0')->where('ship_status', '<>', 'pending')->where('is_comment', 0);
            }
        }
        $orders = $orders->has('items')->orderBy('created_at', 'desc')
            ->paginate(per_page(50));
        //dd(DB::getQueryLog());
        $orderTransformer = new OrderTransformer();
        $orderTransformer->setDefaultIncludes(['items']);
        return $this->response()->paginator($orders, $orderTransformer);
    }

    public function show($id)
    {
        $order = Order::findOrFail($id);
        $this->authorize('own', $order);
        $cache_key = '_order_' . $id . '_logistics';
        // 物流信息
        if (!Cache::get($cache_key) && !in_array($order->ship_status, [Order::SHIP_STATUS_PENDING, Order::SHIP_STATUS_FINISHED]) && !empty($order->ship_data['express_no'])) {
            $logistics = app('logistics')->query($order->ship_data['express_no']);
            if (!empty($logistics['kuaidibird']['status']) && $logistics['kuaidibird']['status'] == 'success') {
                $ship_data = $order->ship_data;
                $ship_data['logistics_status'] = !empty($logistics['kuaidibird']['result']['logistics_status']) ? $logistics['kuaidibird']['result']['logistics_status'] : '';
                $ship_data['logistics_company'] = !empty($logistics['kuaidibird']['result']['logistics_company']) ? $logistics['kuaidibird']['result']['logistics_company'] : '';
                $ship_data['logistics_data'] = !empty($logistics['kuaidibird']['result']['data']) ? $logistics['kuaidibird']['result']['data'] : '';
                $order->ship_data = $ship_data;
                if ($ship_data['logistics_status'] == '3') {
                    $order->received_at = Carbon::now();
                }
                $order->save();
                Cache::put($cache_key, 1, 1800);
            }
        }
        $orderTransformer = new OrderTransformer();
        $orderTransformer->setDefaultIncludes(['items']);
        return $this->response()->item($order, $orderTransformer);
    }

    public function store_web(OrderRequest $request, OrderService $orderService)
    {
        $user = User::find(auth('api')->id());
        $address = UserAddress::find($request->input('address_id'));
        //$identity = UserIdentity::find($request->input('identity_id'));
        $coupon = null;
        if ($request->input('coupon_id')) {
            $coupon = $user->couponCodes()->where('user_coupon_codes.id', $request->input('coupon_id'))->first();
            if (!$coupon) {
                throw new ResourceException('优惠券不存在');
            }
        }
        $order = $orderService->store_web($user, $address, $request->input('items'), $coupon, $request->input('remark'));
        $order = Order::findOrFail($order->id);
        $orderTransformer = new OrderTransformer();
        $orderTransformer->setDefaultIncludes(['items']);
        return $this->response()->item($order, $orderTransformer);
    }

    public function store(OrderRequest $request, OrderService $orderService)
    {
        $user = User::find(auth('api')->id());
        $address = UserAddress::find($request->input('address_id'));
        $identity = UserIdentity::find($request->input('identity_id'));
        $coupon = null;
        if ($request->input('coupon_id')) {
            $coupon = $user->couponCodes()->where('user_coupon_codes.id', $request->input('coupon_id'))->first();
            if (!$coupon) {
                throw new ResourceException('优惠券不存在');
            }
        }
        $order = $orderService->store($user, $address, $request->input('items'), $coupon, $request->input('remark'), $identity, $request->input('integral'), $request->input('card_uuid'));
        $order = Order::findOrFail($order->id);
        $orderTransformer = new OrderTransformer();
        $orderTransformer->setDefaultIncludes(['items']);
        return $this->response()->item($order, $orderTransformer);
    }

    // 充值
    public function recharge(Request $request, OrderService $orderService)
    {
        if (!$request->input('id')) {
            throw new ResourceException('id 参数不能为空');
        }
        $food_stamp_set = FoodStampSet::query()->where('is_blocked', 0)
            ->where('begin_at', '<=', Carbon::now())
            ->where('end_at', '>=', Carbon::now())
            ->where('id', $request->input('id'))
            ->first();
        if (!$food_stamp_set) {
            throw new ResourceException('id 参数无效');
        }
        $user = User::find(auth('api')->id());
        $order = $orderService->recharge($user, $food_stamp_set, $request->input('remark'));
        $order = Order::findOrFail($order->id);
        $orderTransformer = new OrderTransformer();
        $orderTransformer->setDefaultIncludes(['items']);
        return $this->response()->item($order, $orderTransformer);
    }

    public function applyRefund($id, ApplyRefundRequest $request)
    {
        $order = Order::findOrFail($id);
        // 校验订单是否属于当前用户
        $this->authorize('own', $order);
        // 判断订单是否已付款
        if (!$order->paid_at) {
            throw new ResourceException('该订单未支付，不可退款');
        }
        // 判断订单退款状态是否正确
        if ($order->refund_status !== Order::REFUND_STATUS_PENDING) {
            throw new ResourceException('该订单已经申请过退款，请勿重复申请');
        }
        // 将用户输入的退款理由放到订单的 extra 字段中
        $extra = $order->extra ?: [];
        $extra['refund_reason'] = $request->input('reason');
        // 将订单退款状态改为已申请退款
        $order->update([
            'refund_status' => Order::REFUND_STATUS_APPLIED,
            'extra'         => $extra,
        ]);

        return $this->response->created();
    }

    public function closed($id)
    {
        // 订单信息
        $order = Order::findOrFail($id);
        // 校验权限
        $this->authorize('own', $order);
        // 判断订单的状态是否未付款
        if ($order->paid_at) {
            throw new ResourceException('未支付订单才能取消');
        }
        // 关闭订单
        if ($order->closed) {
            throw new ResourceException('订单已经关闭过了');
        }
        // 通过事务执行 sql
        DB::transaction(function () use ($order) {
            if ($order->couponCode) {
                $couponCode = CouponCode::find($order->couponCode->coupon_code_id);
                $couponCode->changeUsed(false);
                $order->couponCode->update(['used' => false, 'used_at' => null]);
            }
            // 将订单的 closed 字段标记为 true，即关闭订单
            $order->update(['closed' => true, 'coupon_code_id' => null]);
            // 积分退还
            if ($order->integral_amount > 0) {
                $description = '订单积分退还';
                (new UserIntegralService())->getIntegral(UserIntegral::GET_METHOD_ORDER_EXCHANGE, $order->user, $order->integral_amount, $description, $order);
            }
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

    public function received($id)
    {
        // 订单信息
        $order = Order::findOrFail($id);
        // 校验权限
        $this->authorize('own', $order);
        // 判断订单的发货状态是否为已发货
        if ($order->ship_status === Order::SHIP_STATUS_PENDING) {
            throw new ResourceException('订单还未发货');
        }
        if ($order->ship_status === Order::SHIP_STATUS_FINISHED) {
            throw new ResourceException('订单已经确认收货了');
        }
        // 更新发货状态为已收到
        $order->update([
            'ship_status' => Order::SHIP_STATUS_FINISHED,
            'finished_at' => Carbon::now()
        ]);

        return $this->response->created();
    }

    // 创建一个新的方法用于接受团购商品下单请求
    public function group(GroupOrderRequest $request, OrderService $orderService)
    {
        $user = User::find(auth('api')->id());
        $sku = ProductSku::find($request->input('sku_id'));
        $address = UserAddress::find($request->input('address_id'));
        $amount = $request->input('amount');
        $coupon = null;
        if ($request->input('coupon_id')) {
            $coupon = $user->couponCodes()->where('user_coupon_codes.id', $request->input('coupon_id'))->first();
            if (!$coupon) {
                throw new ResourceException('优惠券不存在');
            }
        }
        $group = null;
        if ($request->input('group_id')) {
            $group = Group::where('id', $request->input('group_id'))->where('sku_id', $request->input('sku_id'))->where('status', Group::STATUS_WAITING)->first();
            if (!$group) {
                throw new ResourceException('该团状态异常，请重新开团');
            }
        }
        $order = $orderService->group($user, $address, $sku, $amount, $coupon, $group, $request->input('remark'));
        $order = Order::findOrFail($order->id);
        $orderTransformer = new OrderTransformer();
        $orderTransformer->setDefaultIncludes(['items']);
        return $this->response()->item($order, $orderTransformer);
    }

    // 创建一个新的方法用于接受砍价商品下单请求
    public function bargain(BargainOrderRequest $request, OrderService $orderService)
    {
        //DB::connection()->enableQueryLog(); // 开启查询日志
        $user = User::find(auth('api')->id());
        $address = UserAddress::find($request->input('address_id'));
        $identity = UserIdentity::find($request->input('identity_id'));
        $bargain = Bargain::where('id', $request->input('bargain_id'))
            ->where('user_id', auth('api')->id())
            ->where('sku_id', $request->input('sku_id'))
            ->where('status', Bargain::STATUS_SUCCESS)
            //->whereNull('order_id')
            ->whereNull('paid_at')
            ->first();
        $amount = 1;
        //print_r(DB::getQueryLog());exit();
        if (!$bargain) {
            throw new ResourceException('该砍价状态异常');
        }
        $sku = ProductSku::find($request->input('sku_id'));

        $coupon = null;
        if ($request->input('coupon_id')) {
            $coupon = $user->couponCodes()->where('user_coupon_codes.id', $request->input('coupon_id'))->first();
            if (!$coupon) {
                throw new ResourceException('优惠券不存在');
            }
        }
        $order = $orderService->bargain($user, $address, $sku, $amount, $coupon, $bargain, $request->input('remark'), $identity);
        $order = Order::findOrFail($order->id);
        $orderTransformer = new OrderTransformer();
        $orderTransformer->setDefaultIncludes(['items']);
        return $this->response()->item($order, $orderTransformer);
    }

    // 退款处理
    public function handleRefund($id, OrderService $orderService)
    {
        $order = Order::findOrFail($id);
        // 判断订单状态是否正确
//        if ($order->refund_status !== Order::REFUND_STATUS_APPLIED) {
        if ($order->refund_status !== Order::REFUND_STATUS_PENDING || $order->aftersales_status !== Order::AFTERSALES_STATUS_REFUND) {
            throw new ResourceException('订单状态不正确');
        }
        $orderService->refundOrder($order);
        return $this->response->noContent();
    }
    
    //推送快递
    public function push_express(Request $request)
    {
        if (request('orderNo')) {
            $no = request('orderNo');
//            $no = '20230113201727637785';
            $order = Order::where('no', $no)->first();
//            print_r($order);exit;
            if ($order) {
                $ship_data['express_company'] = request('expressCompany');
                $ship_data['express_no'] = request('expressNo');
//                $data['express_company'] = '测试快递';
//                $data['express_no'] = '345678';
//                $ship_data = json_encode($ship_data);
                $order->ship_status = Order::SHIP_STATUS_DELIVERED;
                $order->ship_data = $ship_data;
                $order->delivered_at = Carbon::now();
                $order->save();
            }
        }
        return $this->response->array(['message' => '推送成功', 'status_code' => 200]);
    }
    //推送恒业一般贸易仓
    public function customs_push_hyie(CustomsService $customsService, SplitOrderService $splitOrderService)
    {
        $ids[] = request('order_id');
        if (request('ids')) {
            $ids = array_merge($ids, request('ids'));
        }
//        $ids = array(4479);
        $orders = Order::whereIn('id', $ids)->get();
        foreach ($orders as $order) {
                //推送唐山push_order订单接口
            if($order->warehouse_id == 1 && $order->customs_status == Order::CUSTOMS_STATUS_PENDING && $order->refund_status == Order::REFUND_STATUS_PENDING){
                $data = $this->buildPushOrder($order);
                $items = $this->buildPushOrderItems($order);
                $data['orderList'] = $items;
                $json_data = json_encode($data);
//                print_r($json_data);exit;
                
                $client = new Client();
                $response = $client->request('POST', 'http://27.191.238.186:9091/bodyInventory/ConvertData/1672888744182', [
                    'headers' => ['Content-Type' => 'application/json'],
                    'body' => $json_data
//                    'json' => [
//                        'push_data' => $json_data
//                    ]
                ]);
                $res = json_decode($response->getBody()->getContents(), true);
//                print_r($res);exit;  //{"mailNo":"20230113201727637785","success":true,"message":"订单接收成功"}
                
                //Array(
//                        [mailNo] => 20230113201727637785
//                        [success] => 1
//                        [message] => 订单已存在
//                        )
//                if($res['results']['code'] == 200){
//                  
//                }else{
//                    $order->customs_status = Order::CUSTOMS_STATUS_FAILED;
//                    $order->customs_data = $res['results']['msg'];
//                    $order->save();
//                }
                if($res['success'] == 'true'){
                    $order->customs_status = Order::CUSTOMS_STATUS_SUCCESS;
                    $order->customs_data = $res['message'];
                    $order->save();
                }else{
                    $order->customs_status = Order::CUSTOMS_STATUS_FAILED;
                    $order->customs_data = $res['message'];
                    $order->save();
                }
//                exit;
            }
                
//                
//                $response = $client->request('POST', 'https://api.hihgo.com/channels/push_order', [
//                    'headers' => ['Content-Type' => 'application/json'],
//                    'json' => [
//                        'cid' => '6c84c92c50cb4cfc',
//                        'push_data' => $json_data
//                    ]
//                ]);

//                $order->save();
                //dispatch(new CustomsHyie($order, 1));
//                dispatch_now(new CustomsHyie($order, 1));
            }
    }
    
    public function buildPushOrder($order)
    {
        // 交易时间
//        $payment_time = date('Y-m-d H:i:s', $order->paid_at->timestamp);
        $data['orderNo'] = $order->no; // 订单编号
        $data['buyerName'] = $order->address['contact_name']; // 购买人姓名
        $data['buyerPhone'] = $order->address['contact_phone']; // 购买人电话
        $data['consigneeprov'] = $order->address['province']; // 收货人省份
        $data['consigneecity'] = $order->address['city']; // 收货人市
        $data['consigneedistrict'] = $order->address['district']; // 收货人区
        $data['consigneezip'] = $order->address['zip']; // 收货人邮编 
        $data['consigneeName'] = $order->address['contact_name']; // 收货人姓名
        $data['consigneePhone'] = $order->address['contact_phone']; // 收货人电
        $data['consigneeAddress'] = $order->address['address']; // 收货人地址 
        $data['totalAmount'] = $order->total_amount; // 总金额
        $data['netWeight'] = $this->calculateNetWeight($order); // 重量
        $data['paymentTime'] = date("YmdHis", $order->paid_at->timestamp); // 支付时间
        $data['companyCode'] = 'KNG'; // 公司代码（固定） //Platform
        $data['platformCode'] = 'knty';
        return $data;
    }

    public function buildPushOrderItems($order)
    {
        $items = $order->items;
        foreach ($items as $k => $item) {
            $sku = $item->productSku;
            
            $data[$k]['gnum'] = $sku->no; // 商品序号
            $data[$k]['qty'] = $item->amount * $sku->qty;
            $data[$k]['gCode'] = $sku->barcode; // 商品编码
            $data[$k]['gName'] = $sku->title; //商品名称
            $data[$k]['num'] = $item->amount; // 数量
        }
        return $data;
    }
    
    public function customs_hyie(CustomsService $customsService, SplitOrderService $splitOrderService)
    {
        $ids[] = request('order_id');
        if (request('ids')) {
            $ids = array_merge($ids, request('ids'));
        }
//        $ids = array(2007);
        $orders = Order::whereIn('id', $ids)->get();
        foreach ($orders as $order) {
            if ($order->warehouse->type != 'cross_border') {
                continue;
            }
            $customs_log = $order->customsLogs()->orderBy('created_at', 'desc')->first();
            if (!empty($customs_log->created_at) && (time() - $customs_log->created_at->timestamp) < 900) {
                //continue;
            }

            if ($order->customs_status === Order::CUSTOMS_STATUS_PENDING || $order->customs_status === Order::CUSTOMS_STATUS_FAILED) {
                // 更新订单清关状态
                $order->customs_status = Order::CUSTOMS_STATUS_PROCESSING;
                $order->customs_data = '';

                //先调用支付通知接口 
//                if($order->push_pay == 0){
//                    $client = new Client();
//                    $response = $client->request('POST', 'http://santa-service.santaluma.com/v1/pushPayInfo', [
//                                'headers' => ['Content-Type' => 'application/json'],
//                                'body' => $json_data
//                            ]);
//                    $res = json_decode($response->getBody()->getContents(), true);
//                    if($res['results']['code'] == 200){
//                        $order->push_pay = 1;
//                    }else{
//                        //$this->order->update(['customs_status' => Order::CUSTOMS_STATUS_FAILED, 'customs_data' => $res['results']['msg']]);
//                        $order->customs_status = Order::CUSTOMS_STATUS_PROCESSING;
//                        $order->customs_data = $res['results']['msg'];
//                    }
//                }


                //推送唐山push_order订单接口
//                $data = $this->buildOrder($order);
//                $items = $this->buildOrderItems($order);
//                $data['orderList'] = $items;
//                $json_data = json_encode($data);
//                $client = new Client();
//                
//                $response = $client->request('POST', 'http://santa-service.santaluma.com/v1/pushPayInfo', [
//                    'headers' => ['Content-Type' => 'application/json'],
//                    'body' => $json_data
//                ]);
//                $res = json_decode($response->getBody()->getContents(), true);
//                print_r($res);exit;
//                if($res['results']['code'] == 200){
//                    //支付调用成功后推送到唐山恒业系统
//                    $response = $client->request('POST', 'https://api.hihgo.com/channels/push_order', [
//                        'headers' => ['Content-Type' => 'application/json'],
//                        'json' => [
//                            'cid' => '6c84c92c50cb4cfc',
//                            'push_data' => $json_data
//                        ]
//                    ]);
//                }else{
//                    $order->customs_status = Order::CUSTOMS_STATUS_FAILED;
//                    $order->customs_data = $res['results']['msg'];
//                    $order->save();
//                }
//                exit;
//                
//                $response = $client->request('POST', 'https://api.hihgo.com/channels/push_order', [
//                    'headers' => ['Content-Type' => 'application/json'],
//                    'json' => [
//                        'cid' => '6c84c92c50cb4cfc',
//                        'push_data' => $json_data
//                    ]
//                ]);

                $order->save();
                //dispatch(new CustomsHyie($order, 1));
                dispatch_now(new CustomsHyie($order, 1));
            }
        }
    }


    public function buildOrder($order)
    {
//        $address = UserAddress::find ($order->address['id']);
//        $identity = UserIdentity::find ($order->identity['id']);
        if ($order->parent_id) {
            $parent_order = Order::find($order->parent_id);
            $customs_pay = CustomsPay::where(['order_no' => $parent_order->no])->first();
        } else {
            $customs_pay = CustomsPay::where(['order_no' => $order->no])->first();
        }
        // 交易时间
        $payment_time = date("YmdHis", $order->paid_at->timestamp);
        if ($customs_pay && $pay_data = json_decode($customs_pay->pay_data, true)) {
            if (!empty($pay_data['payExchangeInfoHead']['tradingTime'])) {
                $payment_time = $pay_data['payExchangeInfoHead']['tradingTime'];
            }
        }
        // 支付流水
        $payment_no = $order->payment_no; // 支付流水
        if ($order->payment_method == 'allinpay' && !empty($order->pay_notify_data['trxid'])) {
            $payment_no = $order->pay_notify_data['trxid'];
        }
        $data['logisticsType'] = $order->warehouse->express->code; // 物流类型
        $data['logisticsNo'] = '123456789'; // 物流单号
        $data['orderNo'] = !empty($order->suffix) ? $order->no . '_' . $order->suffix : $order->no; // 订单编号
        $data['orignOrderNo'] = $order->no; // 订单编号
        $data['mainPaymentFlow'] = $payment_no; // 主支付流水
        $data['paymentFlow'] = !empty($order->parent_id) ? $payment_no . '0' . $order->split_number : $payment_no; // 拆单支付流水
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
        return $data;
    }

    public function buildOrderItems($order)
    {
        $items = $order->items;
        foreach ($items as $k => $item) {
            $sku = $item->productSku;
            //$product = $item->product;
            $data[$k]['gnum'] = $sku->no; // 商品序号
            $data[$k]['itemNo'] = $sku->item_no; // 企业商品货号
            $data[$k]['itemName'] = $sku->item_name; // 企业商品名
            $data[$k]['itemRecordNo'] = $sku->item_record_no; // 报税进口
            $data[$k]['gmodel'] = $sku->gmodel; // 商品规格型号
            $data[$k]['country'] = $sku->country; // 原产国
            $data[$k]['qty'] = (string)($sku->qty * $item->amount); // 数量
            $data[$k]['qty1'] = (string)round($sku->qty1 * $item->amount, 5); // 法定数量
            $data[$k]['qty2'] = !empty($sku->qty2 * $item->amount) ? (string)($sku->qty2 * $item->amount) : ''; // 第二数量
            $data[$k]['unit'] = $sku->unit; // 计量单位
            $data[$k]['unit1'] = $sku->unit1; // 法定计量单位
            $data[$k]['unit2'] = !empty($sku->unit2) ? $sku->unit2 : ''; // 第二计量单位
            $data[$k]['price'] = (string)round(($item->price - $item->coupon) / $sku->qty, 2); // 成交总价
            $data[$k]['gCode'] = $sku->gcode; // 商品编码
            $data[$k]['gName'] = $sku->title; // 商品名称
            $data[$k]['num'] = $item->amount; // 数量
        }
        return $data;
    }

    public function calculateNetWeight($order)
    {
        $items = $order->items;
        $net_weight = 0;
        foreach ($items as $k => $item) {
//            $sku = $item->productSku;
//            $net_weight += $item->amount * $sku->net_weight * $sku->qty;
            $net_weight += $item->weight;
        }
        return (string)round($net_weight, 5);
    }

    public function customs(CustomsService $customsService, SplitOrderService $splitOrderService)
    {
        $ids[] = request('order_id');
        if (request('ids')) {
            $ids = array_merge($ids, request('ids'));
        }
        $orders = Order::whereIn('id', $ids)->get();
        foreach ($orders as $order) {
            //$splitOrderService->split($order);
            //$customsService->clearance($order);
            if ($order->warehouse->type != 'cross_border') {
                continue;
            }
            $customs_log = $order->customsLogs()->orderBy('created_at', 'desc')->first();
            if (!empty($customs_log->created_at) && (time() - $customs_log->created_at->timestamp) < 900) {
                continue;
            }
            if ($order->customs_status === Order::CUSTOMS_STATUS_PENDING || $order->customs_status === Order::CUSTOMS_STATUS_FAILED) {
                // 更新订单清关状态
                $order->customs_status = Order::CUSTOMS_STATUS_PROCESSING;
                $order->customs_data = '';
                $order->save();
                dispatch(new Customs($order, 1));
            }
        }
    }

    public function customsCancel(CustomsService $customsService)
    {
        $ids[] = request('order_id');
        if (request('ids')) {
            $ids = array_merge($ids, request('ids'));
        }
        $orders = Order::whereIn('id', $ids)->get();
        foreach ($orders as $order) {
            //$customsService->cancel($order);
            //if ($order->customs_status !== Order::CUSTOMS_STATUS_CANCEL_PROCESSING){
            if (1) {
                // 更新订单清关状态
                $order->customs_status = Order::CUSTOMS_STATUS_CANCEL_PROCESSING;
                $order->save();
                dispatch(new CustomsCancel($order, 1));
            }
        }

    }

    public function customsRefund(CustomsService $customsService)
    {
        $ids[] = request('order_id');
        if (request('ids')) {
            $ids = array_merge($ids, request('ids'));
        }
        $orders = Order::whereIn('id', $ids)->get();
        foreach ($orders as $order) {
            //$customsService->clearance($order);
            //if ($order->customs_status !== Order::CUSTOMS_STATUS_REFUND_PROCESSING){
            if (1) {
                // 更新订单清关状态
                $order->customs_status = Order::CUSTOMS_STATUS_REFUND_PROCESSING;
                $order->save();
                dispatch(new CustomsRefund($order, 1));
            }
        }

    }

    public function customsSuccess(CustomsService $customsService)
    {
        $ids[] = request('order_id');
        if (request('ids')) {
            $ids = array_merge($ids, request('ids'));
        }
        $orders = Order::whereIn('id', $ids)->get();
        foreach ($orders as $order) {
            //$customsService->clearance($order);
            //if ($order->customs_status !== Order::CUSTOMS_STATUS_REFUND_PROCESSING){
            if (1) {
                // 更新订单清关状态
                $order->customs_status = Order::CUSTOMS_STATUS_SUCCESS;
                $order->save();
            }
        }

    }

    public function customsStatus(CustomsService $customsService)
    {
        $order_no = request('order_no');
        $status = request('status');
        $remark = request('remark');
        if (!$order_no) {
            throw new ResourceException('订单号不能为空');
        }
        if (!$status) {
            throw new ResourceException('状态不能为空');
        }
        $out_trade_no = explode('_', $order_no);
        $no = $out_trade_no[0];
        $suffix = !empty($out_trade_no[1]) ? $out_trade_no[1] : '';
        // 找到对应的订单
        $order = Order::where('no', $no)->first();
        if ($order) {
            $customsService->clearanceStatus($order, $status, $remark);
        }
        return $this->response->array(['message' => '操作成功', 'status_code' => 200]);
    }


}
