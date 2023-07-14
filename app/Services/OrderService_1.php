<?php

namespace App\Services;

use App\Events\OrderPaid;
use App\Exceptions\CouponCodeUnavailableException;
use App\Exceptions\InternalException;
use App\Jobs\RefundInstallmentOrder;
use App\Models\Bargain;
use App\Models\CombinationProduct;
use App\Models\Group;
use App\Models\GroupItem;
use App\Models\Level;
use App\Models\OrderItem;
use App\Models\Product;
use App\Models\User;
use App\Models\UserAddress;
use App\Models\Order;
use App\Models\ProductSku;
use App\Models\CouponCode;
use App\Jobs\CloseOrder;
use Carbon\Carbon;
use Dingo\Api\Exception\ResourceException;

class OrderService
{
    protected $splitOrderService;

    public function __construct(SplitOrderService $splitOrderService)
    {
        $this->splitOrderService = $splitOrderService;
    }
    
    public function store_web(User $user, UserAddress $address, $items, CouponCode $coupon = null, $remark = '')
    {
        if ($coupon) {
            $coupon->checkAvailable($user);
        }
        // 开启一个数据库事务
        $order = \DB::transaction(function () use ($user, $address, $items, $coupon, $remark) {
            // 更新此地址的最后使用时间
            $address->update(['last_used_at' => Carbon::now()]);
            //!$identity ?: $identity->update(['last_used_at' => Carbon::now()]);
            // 创建一个订单
            $order = new Order([
                'address' => [ // 将地址信息放入订单中
                    'id' => $address->id,
                    'province' => $address->province,
                    'city' => $address->city,
                    'district' => $address->district,
                    'address' => $address->address,
                    'full_address' => $address->full_address,
                    'zip' => $address->zip,
                    'contact_name' => $address->contact_name,
                    'contact_phone' => $address->contact_phone,
                    'real_name' => $address->real_name,
                    'idcard_no' => $address->idcard_no,
                    'phone' => $address->phone,
                ],
                'identity' => [ // 将证件信息放入订单中
                    'id' => '',
                    'real_name' => $address->real_name,
                    'idcard_no' => $address->idcard_no,
                    'phone' => $address->phone,
                    'idcard_front' => '',
                    'idcard_back' => '',
                ],
                'remark' => $remark,
                'total_amount' => 0,
                'type' => Order::TYPE_NORMAL,
            ]);
            //dd($order);
            // 订单关联到当前用户
            $order->user()->associate($user);
            // 写入数据库
            $order->save();

            $totalAmount = 0;
            // 遍历用户提交的 SKU
            $temp_items = [];
            $warehouse_id = 0;
            $cost_amount = 0;
            $profit_amount = 0;
            foreach ($items as $k => $data) {
                $sku = ProductSku::with('product')->find($data['sku_id']);
                if ($sku->product->warehouse_id){
                    $warehouse_id = $sku->product->warehouse_id;
                };
                $temp_items[$k] = $sku->toArray();
                $temp_items[$k]['amount'] = $data['amount'];
                // 组合装类型
                if ($sku->product->type == Product::TYPE_COMBINATION){
                    $cps = CombinationProduct::where('product_id', $sku->product->id)->get();
                    if (!$cps){
                        continue;
                    }
                    // 组合装明细
                    foreach ($cps as $cp){
                        // 计算税额
                        $tax = $this->splitOrderService->calculateTax($cp->price, $cp->tax_rate);
                        $tax = sprintf("%.2f",$tax);
                        // 成本价
                        $cost = $cp->productSku->min_price;
                        // 利润
                        $profit = $cp->price - $cp->productSku->min_price;
                        if ($profit < 0){
                            throw new ResourceException('销售价格不能低于最低售价');
                        }
                        // 创建一个 OrderItem 并直接与当前订单关联
                        $item = $order->items()->make([
                            'cp_id' => $cp->product_id,
                            'amount' => $cp->amount * $data['amount'],
                            'price' => $cp->price,
                            'tax_rate' => $cp->tax_rate,
                            'tax' => $tax,
                            'cost' => $cost,
                            'profit' => $profit
                        ]);
                        // 成本总额
                        $cost_amount += $cost * $data['amount'] * $cp->amount;
                        // 利润总额
                        $profit_amount += $profit * $data['amount'] * $cp->amount;
                        $item->product()->associate($cp->productSku->product_id);
                        $item->productSku()->associate($cp->productSku);
                        $item->save();
                        $totalAmount += $cp->price * $data['amount'] * $cp->amount;
                        if ($data['amount'] <= 0) {
                            throw new ResourceException('减库存不可小于0');
                        }
                    }
                    // 组合装
                    $item = $order->cpItems()->make([
                        'amount' => $data['amount'],
                        'price' => $sku->price,
                    ]);
                    $item->product()->associate($sku->product_id);
                    $item->productSku()->associate($sku);
                    $item->save();
                }else{
                    $sku_price = $sku->price;
                    $sku_tax_rate = $sku->tax_rate;
                    //等级2的CLUB用户会员价
//                    if($user->level == 2){
//                        $sku_price = $sku->club_price;
//                        $sku_tax_rate = $sku->club_tax_rate;
//                    }
                    // 计算税额
                    //$tax = $this->splitOrderService->calculateTax($sku->price, $sku->tax_rate);
                    $tax = $this->splitOrderService->calculateTax($sku_price, $sku_tax_rate);
                    $tax = sprintf("%.2f",$tax);
                    // 成本价
                    $cost = $sku->min_price;
                    // 利润
                    //$profit = $sku->price - $sku->min_price;
                    $profit = $sku_price - $sku->min_price;
                    if ($profit < 0){
                        throw new ResourceException('销售价格不能低于最低售价');
                    }
                    // 创建一个 OrderItem 并直接与当前订单关联
                    $item = $order->items()->make([
                        'amount' => $data['amount'],
//                        'price' => $sku->price,
//                        'tax_rate' => $sku->tax_rate,
                        'price' => $sku_price,
                        'tax_rate' => $sku_tax_rate,
                        'tax' => $tax,
                        'cost' => $cost,
                        'profit' => $profit
                    ]);
                    // 成本总额
                    $cost_amount += $cost * $data['amount'];
                    // 利润总额
                    $profit_amount += $profit * $data['amount'];
                    $item->product()->associate($sku->product_id);
                    $item->productSku()->associate($sku);
                    $item->save();
                    //$totalAmount += $sku->price * $data['amount'];
                    $totalAmount += $sku_price * $data['amount'];
                    if ($data['amount'] <= 0) {
                        throw new ResourceException('减库存不可小于0');
                    }
                }
//                if ($sku->decreaseStock($data['amount']) <= 0) {
//                    throw new ResourceException('该商品库存不足');
//                }
            }
            // 商品总价
            $product_amount = sprintf("%.2f", $totalAmount);
            // 计算折扣
            $level = Level::where('level', $user->level)->first();
            $discount_amount = 0;
            if ($level) {
                $totalAmount = $level->getAdjustedPrice($totalAmount);
                $discount_amount = sprintf("%.2f", $product_amount - $totalAmount);
            }
            // 计算运费
            $freight = $this->splitOrderService->getTotalFreight($temp_items);
            $taxAmount = $this->splitOrderService->getTotalTaxAmount($temp_items);
            $totalAmount += $freight;
            $totalAmount += $taxAmount;
            // 优惠券金额
            $coupon_amount = 0;
            if ($coupon) {
                // 优惠前的应付金额
                $temp_total_amount = $totalAmount;
                // 总金额已经计算出来了，检查是否符合优惠券规则
                $coupon->checkAvailable($user, $totalAmount);
                // 把订单金额修改为优惠后的金额
                $totalAmount = $coupon->getAdjustedPrice($totalAmount);
                // 将订单与优惠券关联
                $order->couponCode()->associate($coupon->pivot->id);
                // 增加优惠券的用量，需判断返回值
                if ($coupon->changeUsed() <= 0) {
                    throw new ResourceException('该优惠券已被禁用');
                }
                // 更新优惠券使用状态
                $coupon->pivot->update([
                    'used' => 1,
                    'used_at' => Carbon::now(),
                ]);
                // 优惠金额
                $coupon_amount = sprintf("%.2f", $temp_total_amount - $totalAmount);
                // 计算优惠详情
                $order_items = OrderItem::where('order_id', $order->id)->get();
                $coupon_rate = sprintf("%.8f", $coupon_amount / $temp_total_amount);
                $temp_coupon_amount = $coupon_amount;
                foreach ($order_items as $k => $item){
                    $coupon = sprintf("%.2f", ($item['price']+$item['tax']) * $coupon_rate);
                    $item->coupon = $coupon;
                    $item->coupon_amount = $coupon * $item['amount'];
                    $temp_coupon_amount -= $item->coupon_amount;
                    if ((count($order_items)-1) == $k && $temp_coupon_amount > 0){
                        $item->coupon_amount = $item->coupon_amount + $temp_coupon_amount;
                    }
                    $item->save();
                }
            }
            $totalAmount = sprintf("%.2f", $totalAmount);
            if ($totalAmount > 5000){
                throw new ResourceException('商品总金额不能大于5000');
            }
            // 更新订单总金额
            $order->update(['total_amount' => $totalAmount,
                'freight' => $freight,
                'product_amount' => $product_amount,
                'coupon_amount' => $coupon_amount,
                'discount_amount' => $discount_amount,
                'tax_amount' => $taxAmount,
                'cost_amount' => $cost_amount,
                'profit_amount' => $profit_amount,
                'warehouse_id' => $warehouse_id,
            ]);
            // 支付金额0直接支付完成
            if ($totalAmount == 0) {
                $order->update([
                    'paid_at' => Carbon::now()
                ]);
                event(new OrderPaid($order));
            }
            // 将下单的商品从购物车中移除
            $skuIds = collect($items)->pluck('sku_id')->all();
            app(CartService::class)->remove($skuIds);

            return $order;
        });

        // 这里我们直接使用 dispatch 函数
        dispatch(new CloseOrder($order, config('app.order_ttl')));

        return $order;
    }
    
    public function store(User $user, UserAddress $address, $items, CouponCode $coupon = null, $remark = '', $identity)
    {
        if ($coupon) {
            $coupon->checkAvailable($user);
        }
        // 开启一个数据库事务
        $order = \DB::transaction(function () use ($user, $address, $items, $coupon, $remark, $identity) {
            // 更新此地址的最后使用时间
            $address->update(['last_used_at' => Carbon::now()]);
            !$identity ?: $identity->update(['last_used_at' => Carbon::now()]);
            // 创建一个订单
            $order = new Order([
                'address' => [ // 将地址信息放入订单中
                    'id' => $address->id,
                    'province' => $address->province,
                    'city' => $address->city,
                    'district' => $address->district,
                    'address' => $address->address,
                    'full_address' => $address->full_address,
                    'zip' => $address->zip,
                    'contact_name' => $address->contact_name,
                    'contact_phone' => $address->contact_phone,
                    'real_name' => $address->real_name,
                    'idcard_no' => $address->idcard_no,
                ],
                'identity' => [ // 将证件信息放入订单中
                    'id' => $identity ? $identity->id : '',
                    'real_name' => $identity ? $identity->real_name : '',
                    'phone' => $identity ? $identity->phone : '',
                    'idcard_no' => $identity ? $identity->idcard_no : '',
                    'idcard_front' => !empty($identity->idcard_front) ? config('api.img_host') . $identity->idcard_front : '',
                    'idcard_back' => !empty($identity->idcard_back) ? config('api.img_host') . $identity->idcard_back : '',
                ],
                'remark' => $remark,
                'total_amount' => 0,
                'type' => Order::TYPE_NORMAL,
            ]);
            //dd($order);
            // 订单关联到当前用户
            $order->user()->associate($user);
            // 写入数据库
            $order->save();

            $totalAmount = 0;
            // 遍历用户提交的 SKU
            $temp_items = [];
            $warehouse_id = 0;
            $cost_amount = 0;
            $profit_amount = 0;
            foreach ($items as $k => $data) {
                $sku = ProductSku::with('product')->find($data['sku_id']);
                if ($sku->product->warehouse_id){
                    $warehouse_id = $sku->product->warehouse_id;
                };
                $temp_items[$k] = $sku->toArray();
                $temp_items[$k]['amount'] = $data['amount'];
                // 组合装类型
                if ($sku->product->type == Product::TYPE_COMBINATION){
                    $cps = CombinationProduct::where('product_id', $sku->product->id)->get();
                    if (!$cps){
                        continue;
                    }
                    // 组合装明细
                    foreach ($cps as $cp){
                        // 计算税额
                        $tax = $this->splitOrderService->calculateTax($cp->price, $cp->tax_rate);
                        $tax = sprintf("%.2f",$tax);
                        // 成本价
                        $cost = $cp->productSku->min_price;
                        // 利润
                        $profit = $cp->price - $cp->productSku->min_price;
                        if ($profit < 0){
                            throw new ResourceException('销售价格不能低于最低售价');
                        }
                        // 创建一个 OrderItem 并直接与当前订单关联
                        $item = $order->items()->make([
                            'cp_id' => $cp->product_id,
                            'amount' => $cp->amount * $data['amount'],
                            'price' => $cp->price,
                            'tax_rate' => $cp->tax_rate,
                            'tax' => $tax,
                            'cost' => $cost,
                            'profit' => $profit
                        ]);
                        // 成本总额
                        $cost_amount += $cost * $data['amount'] * $cp->amount;
                        // 利润总额
                        $profit_amount += $profit * $data['amount'] * $cp->amount;
                        $item->product()->associate($cp->productSku->product_id);
                        $item->productSku()->associate($cp->productSku);
                        $item->save();
                        $totalAmount += $cp->price * $data['amount'] * $cp->amount;
                        if ($data['amount'] <= 0) {
                            throw new ResourceException('减库存不可小于0');
                        }
                    }
                    // 组合装
                    $item = $order->cpItems()->make([
                        'amount' => $data['amount'],
                        'price' => $sku->price,
                    ]);
                    $item->product()->associate($sku->product_id);
                    $item->productSku()->associate($sku);
                    $item->save();
                }else{
                    $sku_price = $sku->price;
                    $sku_tax_rate = $sku->tax_rate;
                    //等级2的CLUB用户会员价
//                    if($user->level == 2){
//                        $sku_price = $sku->club_price;
//                        $sku_tax_rate = $sku->club_tax_rate;
//                    }
                    // 计算税额
                    //$tax = $this->splitOrderService->calculateTax($sku->price, $sku->tax_rate);
                    $tax = $this->splitOrderService->calculateTax($sku_price, $sku_tax_rate);
                    $tax = sprintf("%.2f",$tax);
                    // 成本价
                    $cost = $sku->min_price;
                    // 利润
                    //$profit = $sku->price - $sku->min_price;
                    $profit = $sku_price - $sku->min_price;
                    if ($profit < 0){
                        throw new ResourceException('销售价格不能低于最低售价');
                    }
                    // 创建一个 OrderItem 并直接与当前订单关联
                    $item = $order->items()->make([
                        'amount' => $data['amount'],
//                        'price' => $sku->price,
//                        'tax_rate' => $sku->tax_rate,
                        'price' => $sku_price,
                        'tax_rate' => $sku_tax_rate,
                        'tax' => $tax,
                        'cost' => $cost,
                        'profit' => $profit
                    ]);
                    // 成本总额
                    $cost_amount += $cost * $data['amount'];
                    // 利润总额
                    $profit_amount += $profit * $data['amount'];
                    $item->product()->associate($sku->product_id);
                    $item->productSku()->associate($sku);
                    $item->save();
                    //$totalAmount += $sku->price * $data['amount'];
                    $totalAmount += $sku_price * $data['amount'];
                    if ($data['amount'] <= 0) {
                        throw new ResourceException('减库存不可小于0');
                    }
                }
//                if ($sku->decreaseStock($data['amount']) <= 0) {
//                    throw new ResourceException('该商品库存不足');
//                }
            }
            // 商品总价
            $product_amount = sprintf("%.2f", $totalAmount);
            // 计算折扣
            $level = Level::where('level', $user->level)->first();
            $discount_amount = 0;
            if ($level) {
                $totalAmount = $level->getAdjustedPrice($totalAmount);
                $discount_amount = sprintf("%.2f", $product_amount - $totalAmount);
            }
            // 计算运费
            $freight = $this->splitOrderService->getTotalFreight($temp_items);
            $taxAmount = $this->splitOrderService->getTotalTaxAmount($temp_items);
            $totalAmount += $freight;
            $totalAmount += $taxAmount;
            // 优惠券金额
            $coupon_amount = 0;
            if ($coupon) {
                // 优惠前的应付金额
                $temp_total_amount = $totalAmount;
                // 总金额已经计算出来了，检查是否符合优惠券规则
                $coupon->checkAvailable($user, $totalAmount);
                // 把订单金额修改为优惠后的金额
                $totalAmount = $coupon->getAdjustedPrice($totalAmount);
                // 将订单与优惠券关联
                $order->couponCode()->associate($coupon->pivot->id);
                // 增加优惠券的用量，需判断返回值
                if ($coupon->changeUsed() <= 0) {
                    throw new ResourceException('该优惠券已被禁用');
                }
                // 更新优惠券使用状态
                $coupon->pivot->update([
                    'used' => 1,
                    'used_at' => Carbon::now(),
                ]);
                // 优惠金额
                $coupon_amount = sprintf("%.2f", $temp_total_amount - $totalAmount);
                // 计算优惠详情
                $order_items = OrderItem::where('order_id', $order->id)->get();
                $coupon_rate = sprintf("%.8f", $coupon_amount / $temp_total_amount);
                $temp_coupon_amount = $coupon_amount;
                foreach ($order_items as $k => $item){
                    $coupon = sprintf("%.2f", ($item['price']+$item['tax']) * $coupon_rate);
                    $item->coupon = $coupon;
                    $item->coupon_amount = $coupon * $item['amount'];
                    $temp_coupon_amount -= $item->coupon_amount;
                    if ((count($order_items)-1) == $k && $temp_coupon_amount > 0){
                        $item->coupon_amount = $item->coupon_amount + $temp_coupon_amount;
                    }
                    $item->save();
                }
            }
            $totalAmount = sprintf("%.2f", $totalAmount);
            if ($totalAmount > 5000){
                throw new ResourceException('商品总金额不能大于5000');
            }
            // 更新订单总金额
            $order->update(['total_amount' => $totalAmount,
                'freight' => $freight,
                'product_amount' => $product_amount,
                'coupon_amount' => $coupon_amount,
                'discount_amount' => $discount_amount,
                'tax_amount' => $taxAmount,
                'cost_amount' => $cost_amount,
                'profit_amount' => $profit_amount,
                'warehouse_id' => $warehouse_id,
            ]);
            // 支付金额0直接支付完成
            if ($totalAmount == 0) {
                $order->update([
                    'paid_at' => Carbon::now()
                ]);
                event(new OrderPaid($order));
            }
            // 将下单的商品从购物车中移除
            $skuIds = collect($items)->pluck('sku_id')->all();
            app(CartService::class)->remove($skuIds);

            return $order;
        });

        // 这里我们直接使用 dispatch 函数
        dispatch(new CloseOrder($order, config('app.order_ttl')));

        return $order;
    }

    public function group(User $user, UserAddress $address, ProductSku $sku, $amount, CouponCode $coupon = null, Group $group = null, $remark)
    {

        // 开启事务
        $order = \DB::transaction(function () use ($amount, $sku, $user, $address, $coupon, $group, $remark) {
            // 更新地址最后使用时间
            $address->update(['last_used_at' => Carbon::now()]);
            $totalAmount = $sku->product->group->price * $amount;
            // 创建一个订单
            $order = new Order([
                'address' => [ // 将地址信息放入订单中
                    'address' => $address->full_address,
                    'zip' => $address->zip,
                    'contact_name' => $address->contact_name,
                    'contact_phone' => $address->contact_phone,
                ],
                'remark' => $remark,
                'total_amount' => $totalAmount,
                'type' => Order::TYPE_GROUP,
            ]);
            // 订单关联到当前用户
            $order->user()->associate($user);
            // 写入数据库
            $order->save();
            // 创建一个新的订单项并与 SKU 关联
            $item = $order->items()->make([
                'amount' => $amount,
                'price' => $sku->product->group->price,
            ]);
            $item->product()->associate($sku->product_id);
            $item->productSku()->associate($sku);
            $item->save();
            // 扣减对应 SKU 库存
            if ($amount <= 0) {
                throw new ResourceException('减库存不可小于0');
            }
//            if ($sku->decreaseStock($amount) <= 0) {
//                throw new ResourceException('该商品库存不足');
//            }
            if ($coupon) {
                // 总金额已经计算出来了，检查是否符合优惠券规则
                $coupon->checkAvailable($user, $totalAmount);
                // 把订单金额修改为优惠后的金额
                $totalAmount = $coupon->getAdjustedPrice($totalAmount);
                // 将订单与优惠券关联
                $order->couponCode()->associate($coupon);
                // 增加优惠券的用量，需判断返回值
                if ($coupon->changeUsed() <= 0) {
                    throw new ResourceException('该优惠券已被禁用');
                }
                // 更新优惠券使用状态
                $coupon->pivot->update([
                    'used' => 1,
                    'used_at' => Carbon::now(),
                ]);
            }
            $totalAmount = sprintf("%.2f", $totalAmount);
            // 更新订单总金额
            $order->update(['total_amount' => $totalAmount]);
            // 创建开团信息
            $is_head = 0;
            if (!$group) {
                $is_head = 1;
                $group = new Group([
                    'order_id' => $order->id,
                    'product_id' => $sku->product->id,
                    'sku_id' => $sku->id,
                    'target_count' => $sku->product->group->target_count,
                    'user_count' => 1,
                    'status' => Group::STATUS_PENDING,
                ]);
                // 团购关联到当前用户
                $group->user()->associate($user);
                // 写入数据库
                $group->save();
            }

            $group_item = new GroupItem([
                'group_id' => $group->id,
                'order_id' => $order->id,
                'product_id' => $sku->product->id,
                'sku_id' => $sku->id,
                'is_head' => $is_head,
                'status' => GroupItem::STATUS_PENDING,
            ]);
            // 团购关联到当前用户
            $group_item->user()->associate($user);
            // 写入数据库
            $group_item->save();
            return $order;
        });

        // 剩余秒数与默认订单关闭时间取较小值作为订单关闭时间
        dispatch(new CloseOrder($order, config('app.order_ttl')));

        return $order;
    }

    public function bargain(User $user, UserAddress $address, ProductSku $sku, $amount, CouponCode $coupon = null, Bargain $bargain = null, $remark)
    {

        // 开启事务
        $order = \DB::transaction(function () use ($amount, $sku, $user, $address, $coupon, $bargain, $remark) {
            // 更新地址最后使用时间
            $address->update(['last_used_at' => Carbon::now()]);
            $totalAmount = $bargain->current_price * $amount;
            // 创建一个订单
            $order = new Order([
                'address' => [ // 将地址信息放入订单中
                    'address' => $address->full_address,
                    'zip' => $address->zip,
                    'contact_name' => $address->contact_name,
                    'contact_phone' => $address->contact_phone,
                ],
                'remark' => $remark,
                'total_amount' => $totalAmount,
                'type' => Order::TYPE_BARGAIN,
            ]);
            // 订单关联到当前用户
            $order->user()->associate($user);
            // 写入数据库
            $order->save();
            // 创建一个新的订单项并与 SKU 关联
            $item = $order->items()->make([
                'amount' => $amount,
                'price' => $bargain->current_price,
            ]);
            $item->product()->associate($sku->product_id);
            $item->productSku()->associate($sku);
            $item->save();
            // 扣减对应 SKU 库存
            if ($amount <= 0) {
                throw new ResourceException('减库存不可小于0');
            }
//            if ($sku->decreaseStock($amount) <= 0) {
//                throw new ResourceException('该商品库存不足');
//            }
            if ($coupon) {
                // 总金额已经计算出来了，检查是否符合优惠券规则
                $coupon->checkAvailable($user, $totalAmount);
                // 把订单金额修改为优惠后的金额
                $totalAmount = $coupon->getAdjustedPrice($totalAmount);
                // 将订单与优惠券关联
                $order->couponCode()->associate($coupon);
                // 增加优惠券的用量，需判断返回值
                if ($coupon->changeUsed() <= 0) {
                    throw new ResourceException('该优惠券已被禁用');
                }
                // 更新优惠券使用状态
                $coupon->pivot->update([
                    'used' => 1,
                    'used_at' => Carbon::now(),
                ]);
            }
            // 更新订单总金额
            $totalAmount = sprintf("%.2f", $totalAmount);
            $order->update(['total_amount' => $totalAmount]);
            // 创建开团信息
            $bargain->update(['order_id' => $order->id]);
            return $order;
        });

        // 剩余秒数与默认订单关闭时间取较小值作为订单关闭时间
        dispatch(new CloseOrder($order, config('app.order_ttl')));

        return $order;
    }

    public function refundOrder(Order $order)
    {
        // 判断该订单的支付方式
        switch ($order->payment_method) {
            case 'wechat':
                // 生成退款订单号
                $refundNo = Order::getAvailableRefundNo();
                app('wechat_pay')->refund([
                    'out_trade_no' => $order->no . '_' . $order->suffix,
                    'total_fee' => $order->total_amount * 100,
                    'refund_fee' => $order->total_amount * 100,
                    'out_refund_no' => $refundNo,
                    'notify_url' => app('Dingo\Api\Routing\UrlGenerator')->version('v1')->route('payment.wechat.refund_notify'),
                ]);
                $order->update([
                    'closed' => true,
                    'refund_no' => $refundNo,
                    'refund_status' => Order::REFUND_STATUS_PROCESSING,
                ]);
                break;
            case 'allinpay':
                // 生成退款订单号
                $refundNo = Order::getAvailableRefundNo();
                $no = !empty($order->suffix) ? $order->no.'_'.$order->suffix : $order->no;

                // 拆单订单
                if ($order->parent_id){
                    $parent_order = Order::find($order->parent_id);
                    if ($parent_order){
                        $no = !empty($parent_order->suffix) ? $parent_order->no.'_'.$parent_order->suffix : $parent_order->no;
                    }
                }
                if ($order->paid_at->isToday() && !$order->parent_id){
                    $res = (new AllinpayService())->cancel([
                        'out_trade_no' => $no,
                        'total_fee'    => $order->total_amount * 100,
                        'out_refund_no' => $refundNo,
                    ]);
                }else{
                    $res = (new AllinpayService())->refund([
                        'out_trade_no' => $no,
                        'total_fee'    => $order->total_amount * 100,
                        'out_refund_no' => $refundNo,
                    ]);
                }
                if ($res['trxstatus'] == '0000') {
                    //更新退款状态
                    $order->update([
                        'closed' => true,
                        'refund_no' => $refundNo,
                        'refund_status' => Order::REFUND_STATUS_SUCCESS,
                    ]);
                }
                break;
//            case 'alipay':
//                $refundNo = Order::getAvailableRefundNo();
//                $ret = app('alipay')->refund([
//                    'out_trade_no' => $order->no,
//                    'refund_amount' => $order->total_amount,
//                    'out_request_no' => $refundNo,
//                ]);
//                if ($ret->sub_code) {
//                    $extra = $order->extra;
//                    $extra['refund_failed_code'] = $ret->sub_code;
//                    $order->update([
//                        'refund_no' => $refundNo,
//                        'refund_status' => Order::REFUND_STATUS_FAILED,
//                        'extra' => $extra,
//                    ]);
//                } else {
//                    $order->update([
//                        'refund_no' => $refundNo,
//                        'refund_status' => Order::REFUND_STATUS_SUCCESS,
//                    ]);
//                }
//                break;
//            case 'installment':
//                $order->update([
//                    'refund_no' => Order::getAvailableRefundNo(), // 生成退款订单号
//                    'refund_status' => Order::REFUND_STATUS_PROCESSING, // 将退款状态改为退款中
//                ]);
//                // 触发退款异步任务
//                dispatch(new RefundInstallmentOrder($order));
//                break;
            default:
                throw new ResourceException('未知订单支付方式：' . $order->payment_method);
                break;
        }
    }

    public function seckill(User $user, array $addressData, ProductSku $sku)
    {
        $order = \DB::transaction(function () use ($user, $addressData, $sku) {
            // 创建一个订单
            $order = new Order([
                'address' => [ // address 字段直接从 $addressData 数组中读取
                    'address' => $addressData['province'] . $addressData['city'] . $addressData['district'] . $addressData['address'],
                    'zip' => $addressData['zip'],
                    'contact_name' => $addressData['contact_name'],
                    'contact_phone' => $addressData['contact_phone'],
                ],
                'remark' => '',
                'total_amount' => $sku->price,
                'type' => Order::TYPE_SECKILL,
            ]);
            // 订单关联到当前用户
            $order->user()->associate($user);
            // 写入数据库
            $order->save();
            // 创建一个新的订单项并与 SKU 关联
            $item = $order->items()->make([
                'amount' => 1, // 秒杀商品只能一份
                'price' => $sku->price,
            ]);
            $item->product()->associate($sku->product_id);
            $item->productSku()->associate($sku);
            $item->save();
            // 扣减对应 SKU 库存
            if ($sku->decreaseStock(1) <= 0) {
                throw new ResourceException('该商品库存不足');
            }
            \Redis::decr('seckill_sku_' . $sku->id);

            return $order;
        });
        // 秒杀订单的自动关闭时间与普通订单不同
        dispatch(new CloseOrder($order, config('app.seckill_order_ttl')));

        return $order;
    }
}
