<?php

namespace App\Services;

use App\Events\OrderPaid;
use App\Jobs\CloseOrder;
use App\Models\CombinationProduct;
use App\Models\CouponCode;
use App\Models\Level;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Product;
use App\Models\ProductSku;
use App\Models\User;
use App\Models\UserAddress;
use Carbon\Carbon;
use Dingo\Api\Exception\ResourceException;

class ChannelOrderService
{

    public function store($channel, $push_data, $sign)
    {
        if (!$this->verifySign($channel, $push_data, $sign)) {
            throw new ResourceException('签名验证失败');
        }
        // 开启一个数据库事务
        $order = \DB::transaction(function () use ($channel, $push_data, $sign) {
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
                if ($sku->product->warehouse_id) {
                    $warehouse_id = $sku->product->warehouse_id;
                };
                $temp_items[$k] = $sku->toArray();
                $temp_items[$k]['amount'] = $data['amount'];
                // 组合装类型
                if ($sku->product->type == Product::TYPE_COMBINATION) {
                    $cps = CombinationProduct::where('product_id', $sku->product->id)->get();
                    if (!$cps) {
                        continue;
                    }
                    // 组合装明细
                    foreach ($cps as $cp) {
                        // 计算税额
                        $tax = $this->splitOrderService->calculateTax($cp->price, $cp->tax_rate);
                        $tax = sprintf("%.2f", $tax);
                        // 成本价
                        $cost = $cp->productSku->min_price;
                        // 利润
                        $profit = $cp->price - $cp->productSku->min_price;
                        if ($profit < 0) {
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
                } else {
                    // 计算税额
                    $tax = $this->splitOrderService->calculateTax($sku->price, $sku->tax_rate);
                    $tax = sprintf("%.2f", $tax);
                    // 成本价
                    $cost = $sku->min_price;
                    // 利润
                    $profit = $sku->price - $sku->min_price;
                    if ($profit < 0) {
                        throw new ResourceException('销售价格不能低于最低售价');
                    }
                    // 创建一个 OrderItem 并直接与当前订单关联
                    $item = $order->items()->make([
                        'amount' => $data['amount'],
                        'price' => $sku->price,
                        'tax_rate' => $sku->tax_rate,
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
                    $totalAmount += $sku->price * $data['amount'];
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
                foreach ($order_items as $k => $item) {
                    $coupon = sprintf("%.2f", ($item['price'] + $item['tax']) * $coupon_rate);
                    $item->coupon = $coupon;
                    $item->coupon_amount = $coupon * $item['amount'];
                    $temp_coupon_amount -= $item->coupon_amount;
                    if ((count($order_items) - 1) == $k && $temp_coupon_amount > 0) {
                        $item->coupon_amount = $item->coupon_amount + $temp_coupon_amount;
                    }
                    $item->save();
                }
            }
            $totalAmount = sprintf("%.2f", $totalAmount);
            if ($totalAmount > 5000) {
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

    public function verifySign($channel, $push_data, $sign)
    {
        $verify_data = [
            'channel_id' => $channel->id,
            'push_data' => $push_data,
            'secret_key' => $channel->secret_key
        ];
        if (md5(json_encode($verify_data)) === $sign) {
            return true;
        }
        return false;
    }
    
    public function delivered($push_data)
    {
        $order = Order::where('no', $push_data['orderNo'])->first();
        if($order){
            $order->update([
               'customs_status' => $push_data['customs_status'],
               'ship_status' => $push_data['ship_status'],
               'ship_data' => $push_data['ship_data'],
               'delivered_at' => Carbon::now(),
            ]);
        }
    }

}
