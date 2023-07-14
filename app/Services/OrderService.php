<?php

namespace App\Services;

use App\Events\OrderPaid;
use App\Jobs\CloseOrder;
use App\Models\Bargain;
use App\Models\CombinationProduct;
use App\Models\CouponCode;
use App\Models\Group;
use App\Models\GroupItem;
use App\Models\Level;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Product;
use App\Models\ProductSku;
use App\Models\ProductSkuCard;
use App\Models\SupplierStore;
use App\Models\User;
use App\Models\UserAddress;
use App\Models\UserFoodStamp;
use App\Models\UserGift;
use App\Models\UserGiftItem;
use App\Models\UserIntegral;
use App\Models\UserSecondaryCard;
use App\Models\WriteOffLog;
use Carbon\Carbon;
use Dingo\Api\Exception\ResourceException;
use Illuminate\Support\Facades\Storage;
use Milon\Barcode\DNS1D;
use Milon\Barcode\DNS2D;

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
                'address'      => [ // 将地址信息放入订单中
                    'id'            => $address->id,
                    'province'      => $address->province,
                    'city'          => $address->city,
                    'district'      => $address->district,
                    'address'       => $address->address,
                    'full_address'  => $address->full_address,
                    'zip'           => $address->zip,
                    'contact_name'  => $address->contact_name,
                    'contact_phone' => $address->contact_phone,
                    'real_name'     => $address->real_name,
                    'idcard_no'     => $address->idcard_no,
                    'phone'         => $address->phone,
                ],
                'identity'     => [ // 将证件信息放入订单中
                    'id'           => '',
                    'real_name'    => $address->real_name,
                    'idcard_no'    => $address->idcard_no,
                    'phone'        => $address->phone,
                    'idcard_front' => '',
                    'idcard_back'  => '',
                ],
                'remark'       => $remark,
                'total_amount' => 0,
                'type'         => Order::TYPE_NORMAL,
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
//                        $tax = $this->splitOrderService->calculateTax($cp->price, $cp->tax_rate);
//                        $tax = sprintf("%.2f",$tax);
                        $price = sprintf("%.2f", ($cp->tax_price / (1 + $cp->tax_rate / 100)));
                        $tax = sprintf("%.2f", ($cp->tax_price - $price));
                        // 成本价
                        $cost = $cp->productSku->min_price;
                        // 利润
                        $profit = $cp->price - $cp->productSku->min_price;
                        if ($profit < 0) {
//                            throw new ResourceException('销售价格不能低于最低售价');
                        }
                        // 创建一个 OrderItem 并直接与当前订单关联
                        $item = $order->items()->make([
                            'cp_id'    => $cp->product_id,
                            'amount'   => $cp->amount * $data['amount'],
                            'qty'      => $sku->qty,
                            'price'    => $price,
                            'tax_rate' => $cp->tax_rate,
                            'tax'      => $tax,
                            'cost'     => $cost,
                            'profit'   => $profit
                        ]);
                        // 成本总额
                        $cost_amount += $cost * $data['amount'] * $cp->amount;
                        // 利润总额
                        $profit_amount += $profit * $data['amount'] * $cp->amount;
                        $item->product()->associate($cp->productSku->product_id);
                        $item->productSku()->associate($cp->productSku);
                        $item->save();
                        $totalAmount += $price * $data['amount'] * $cp->amount;
                        if ($data['amount'] <= 0) {
                            throw new ResourceException('减库存不可小于0');
                        }
                    }
                    // 组合装
                    $item = $order->cpItems()->make([
                        'amount' => $data['amount'],
                        'price'  => $sku->tax_price,
                    ]);
                    $item->product()->associate($sku->product_id);
                    $item->productSku()->associate($sku);
                    $item->save();
                } else {
                    $sku_price = $sku->price;
                    $sku_tax_rate = $sku->tax_rate;
                    //等级2的CLUB用户会员价
//                    if($user->level == 2 && $user->shop_id == 1001){
//                        $sku_price = $sku->club_price;
//                        $sku_tax_rate = $sku->club_tax_rate;
//                    }
                    // 计算税额
                    //$tax = $this->splitOrderService->calculateTax($sku->price, $sku->tax_rate);
                    $tax = $this->splitOrderService->calculateTax($sku_price, $sku_tax_rate);
                    $tax = sprintf("%.2f", $tax);
                    // 成本价
                    $cost = $sku->min_price;
                    // 利润
                    //$profit = $sku->price - $sku->min_price;
                    $profit = $sku_price - $sku->min_price;
                    if ($profit < 0) {
//                        throw new ResourceException('销售价格不能低于最低售价');
                    }
                    // 创建一个 OrderItem 并直接与当前订单关联
                    $item = $order->items()->make([
                        'amount'   => $data['amount'],
                        'qty'      => $sku->qty,
//                        'price' => $sku->price,
//                        'tax_rate' => $sku->tax_rate,
                        'price'    => $sku_price,
                        'tax_rate' => $sku_tax_rate,
                        'tax'      => $tax,
                        'cost'     => $cost,
                        'profit'   => $profit
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
                    'used'    => 1,
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
//            if ($totalAmount > 5000) {
//                throw new ResourceException('商品总金额不能大于5000');
//            }
            // 更新订单总金额
            $order->update(['total_amount'    => $totalAmount,
                            'freight'         => $freight,
                            'product_amount'  => $product_amount,
                            'coupon_amount'   => $coupon_amount,
                            'discount_amount' => $discount_amount,
                            'tax_amount'      => $taxAmount,
                            'cost_amount'     => $cost_amount,
                            'profit_amount'   => $profit_amount,
                            'warehouse_id'    => $warehouse_id,
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

    public function store(User $user, UserAddress $address = null, $items, CouponCode $coupon = null, $remark = '', $identity, $integral = 0, $card_uuid = '')
    {
        if ($coupon) {
            $coupon->checkAvailable($user);
        }
        // 开启一个数据库事务
        $order = \DB::transaction(function () use ($user, $address, $items, $coupon, $remark, $identity, $integral, $card_uuid) {
            if ($card_uuid) {
                $card = UserSecondaryCard::query()
                    ->where('uuid', $card_uuid)
                    ->where('user_id', $user->id)
                    ->lockForUpdate()
                    ->first();
                if (!$card) {
                    throw new ResourceException('无效的card_uuid');
                }
                if ($card->is_close) {
                    throw new ResourceException('该卡已关闭使用');
                }
            }
            if ($integral) {
                $user = User::where('id', $user->id)
                    ->lockForUpdate()
                    ->first();
                if ($user->integral < $integral) {
                    throw new ResourceException('积分不足');
                }
            }
            // 更新此地址的最后使用时间
            !$address ?: $address->update(['last_used_at' => Carbon::now()]);
            !$identity ?: $identity->update(['last_used_at' => Carbon::now()]);
            // 创建一个订单
            $order = new Order([
                'address'      => [ // 将地址信息放入订单中
                    'id'            => $address->id ?? '',
                    'province'      => $address->province ?? '',
                    'city'          => $address->city ?? '',
                    'district'      => $address->district ?? '',
                    'address'       => $address->address ?? '',
                    'full_address'  => $address->full_address ?? '',
                    'zip'           => $address->zip ?? '',
                    'contact_name'  => $address->contact_name ?? '',
                    'contact_phone' => $address->contact_phone ?? '',
                    'real_name'     => $address->real_name ?? '',
                    'idcard_no'     => $address->idcard_no ?? '',
                ],
                'identity'     => [ // 将证件信息放入订单中
                    'id'           => $identity ? $identity->id : '',
                    'real_name'    => $identity ? $identity->real_name : '',
                    'phone'        => $identity ? $identity->phone : '',
                    'idcard_no'    => $identity ? $identity->idcard_no : '',
                    'idcard_front' => !empty($identity->idcard_front) ? config('api.img_host') . $identity->idcard_front : '',
                    'idcard_back'  => !empty($identity->idcard_back) ? config('api.img_host') . $identity->idcard_back : '',
                ],
                'remark'       => $remark,
                'total_amount' => 0,
                'type'         => Order::TYPE_NORMAL,
            ]);
            //dd($order);
            // 订单关联到当前用户
            $order->user()->associate($user);
            // 写入数据库
            $order->save();
            
            $total_weight = 0;
            $totalAmount = 0;
            $taxAmount = 0;
            // 遍历用户提交的 SKU
            $temp_items = [];
            $warehouse_id = 0;
            $cost_amount = 0;
            $profit_amount = 0;
            $integral_amount = 0;
            $order_type = !$address ? Order::TYPE_GIFT : Order::TYPE_NORMAL;
            $user_food_stamp_description = '';
            foreach ($items as $k => $data) {
                $sku = ProductSku::with('product')->find($data['sku_id']);
                if ($sku->product->warehouse_id) {
                    $warehouse_id = $sku->product->warehouse_id;
                };
                $temp_items[$k] = $sku->toArray();
                $temp_items[$k]['amount'] = $data['amount'];

                $temp_items[$k]['price'] = sprintf("%.2f", ($sku->tax_price / (1 + $sku->tax_rate / 100)));
                $temp_items[$k]['tax_rate'] = $sku->tax_rate;
                if ($user->level == 2 && $user->shop_id == 1001) {
//                    $temp_items[$k]['price'] = sprintf("%.2f", ($sku->club_tax_price / (1 + $sku->tax_rate / 100)));
//                    $temp_items[$k]['price'] = $sku->club_price;
//                    $temp_items[$k]['tax_rate'] = $sku->club_tax_rate;
                }
                if ($sku->product->type == Product::TYPE_SELECT) {
                    $order_type = Product::TYPE_SELECT;
                }
                // 组合装类型
                if ($sku->product->type == Product::TYPE_COMBINATION) {
                    $cps = CombinationProduct::where('product_id', $sku->product->id)->get();
                    if (!$cps) {
                        continue;
                    }
                    // 组合装明细
                    foreach ($cps as $cp) {
                        // 计算税额
                        //$cp_price = $cp->price;
                        $tax_price = $cp->tax_price;
                        if ($user->level == 2 && $user->shop_id == 1001) {
//                            $cp_price = $cp->club_price;
//                            $cp_tax_rate = $cp->club_tax_rate;
                            $tax_price = $cp->club_tax_price;
                        }
                        $cp_tax_rate = $cp->tax_rate;
                        $cp_price = sprintf("%.2f", ($tax_price / (1 + $cp_tax_rate / 100)));
                        $temp_items[$k]['price'] = $cp_price;
                        $temp_items[$k]['tax_rate'] = $cp_tax_rate;
//                        $tax = $this->splitOrderService->calculateTax($cp_price, $cp_tax_rate);
//                        $tax = sprintf("%.2f",$tax);
                        $tax = sprintf("%.2f", ($tax_price - $cp_price));
                        // 成本价
                        $cost = $cp->productSku->club_tax_price;
                        // 利润
                        $profit = $tax_price - $cp->productSku->club_tax_price;
                        if ($profit < 0) {
//                            throw new ResourceException('销售价格不能低于最低售价');
                        }
                        // 创建一个 OrderItem 并直接与当前订单关联
                        $item = $order->items()->make([
                            'cp_id'    => $cp->product_id,
                            'amount'   => $cp->amount * $data['amount'],
                            'qty'      => $sku->qty * $cp->amount * $data['amount'],
                            'weight'   => $sku->qty * $cp->amount *$sku->weight,
                            'price'    => $cp_price,
                            'tax_rate' => $cp_tax_rate,
                            'tax'      => $tax,
                            'cost'     => $cost,
                            'profit'   => $profit
                        ]);
                        // 成本总额
                        $cost_amount += $cost * $data['amount'] * $cp->amount;
                        // 利润总额
                        $profit_amount += $profit * $data['amount'] * $cp->amount;
                        $item->product()->associate($cp->productSku->product_id);
                        $item->productSku()->associate($cp->productSku);
                        $item->save();
                        $totalAmount += $cp_price * $data['amount'] * $cp->amount;
                        $taxAmount += $tax * $data['amount'] * $cp->amount;
                        if ($data['amount'] <= 0) {
                            throw new ResourceException('减库存不可小于0');
                        }
                    }
                    //print_r($totalAmount);exit;
                    // 组合装
                    $item = $order->cpItems()->make([
                        'amount' => $data['amount'],
                        'price'  => sprintf("%.2f", $totalAmount),
                    ]);
                    //print_r($item);exit;
                    $item->product()->associate($sku->product_id);
                    $item->productSku()->associate($sku);
                    $item->save();
                } else {
//                    $sku_price = $sku->price;
                    $tax_price = $sku->tax_price;
                    //等级用户会员价
                    if ($user->level == 1) {
//                        $sku_price = $sku->club_price;
                        $tax_price = $sku->member_price;
                    }
                    if ($user->level == 2) {
//                        $sku_price = $sku->club_price;
                        $tax_price = $sku->club_price;
                    }
                    $sku_tax_rate = $sku->tax_rate;
                    $sku_price = sprintf("%.2f", ($tax_price / (1 + $sku_tax_rate / 100)));
                    // 计算税额
                    //$tax = $this->splitOrderService->calculateTax($sku->price, $sku->tax_rate);
//                    $tax = $this->splitOrderService->calculateTax($sku_price, $sku_tax_rate);
//                    $tax = sprintf("%.2f",$tax);
                    $tax = sprintf("%.2f", ($tax_price - $sku_price));
                    // 成本价
                    $cost = $sku->club_tax_price;
                    // 利润
                    //$profit = $sku->price - $sku->min_price;
                    $profit = $tax_price - $sku->club_tax_price;
                    if ($profit < 0) {
//                        throw new ResourceException('销售价格不能低于最低售价');
                    }
                    // 创建一个 OrderItem 并直接与当前订单关联
                    $item = $order->items()->make([
                        'amount'    => $data['amount'],
                        'qty'       => $data['amount']*$sku->qty,
                        'weight'    => sprintf("%.2f",$data['amount']*$sku->qty*$sku->weight),
                        'price'     => $sku_price,
                        'tax_rate'  => $sku_tax_rate,
                        'tax'       => $tax,
                        'cost'      => $cost,
                        'profit'    => $profit,
                        'sku_title' => $sku->title,
                        'sku_image' => $sku->product->cover ?? '',
                        'sku_price' => $sku->price,
                    ]);
                    // 成本总额
                    $cost_amount += $cost * $data['amount'];
                    // 利润总额
                    $profit_amount += $profit * $data['amount'];
                    $item->product()->associate($sku->product_id);
                    $item->productSku()->associate($sku);
                    $item->save();
                    //$totalAmount += $sku->price * $data['amount'];
                    $total_weight += sprintf("%.2f", $sku->weight * $data['amount'] * $sku->qty);
                    $totalAmount += $sku_price * $data['amount'];
                    $taxAmount += $tax * $data['amount'];
                    if ($data['amount'] <= 0) {
                        throw new ResourceException('减库存不可小于0');
                    }
                    $user_food_stamp_description .= $sku->title . ' * ' . $data['amount'] . '，';
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
//            $freight = $this->splitOrderService->getTotalFreight($temp_items);
            $freight = $this->splitOrderService->getTotalFreight($temp_items, $total_weight, $address->id);
            //$taxAmount = $this->splitOrderService->getTotalTaxAmount($temp_items);
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
                    'used'    => 1,
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
//            if ($totalAmount > 5000) {
//                throw new ResourceException('商品总金额不能大于5000');
//            }
            if ($integral > 0 && $totalAmount > 0) {
                $integral_amount = $integral > $totalAmount * 100 ? $totalAmount * 100 : $integral;
                $totalAmount = $totalAmount * 100 - $integral_amount;
                $totalAmount = sprintf("%.2f", $totalAmount / 100);
                // 兑换积分
                $description = '订单金额抵扣';
                (new UserIntegralService())->useIntegral(UserIntegral::USE_METHOD_ORDER_DEDUCTION, $user, $integral_amount, $description, $order);
            }
            // 更新订单总金额
            $order->update(['total_amount'    => $totalAmount,
                            'freight'         => $freight,
                            'product_amount'  => $product_amount,
                            'coupon_amount'   => $coupon_amount,
                            'discount_amount' => $discount_amount,
                            'tax_amount'      => $taxAmount,
                            'cost_amount'     => $cost_amount,
                            'profit_amount'   => $profit_amount,
                            'integral_amount' => $integral_amount,
                            'warehouse_id'    => $warehouse_id,
                            'type'            => $order_type,
            ]);
            // 支付金额0直接支付完成
            if ($totalAmount == 0) {
                $order->update([
                    'paid_at' => Carbon::now()
                ]);
            } 
//            else {
//                if ($card_uuid) {
//                    if ($card->masterUser->food_stamp < $totalAmount) {
//                        throw new ResourceException('粮票余额不足');
//                    }
//                    $card_no = $card->secondary_card_no;
//                    $user_food_stamp_description = '副卡购买-' . $user_food_stamp_description;
//                    $user_food_stamp_action_type = UserFoodStamp::ACTION_TYPE_SECONDARY_ORDER_PAY;
//                } else {
//                    if ($user->food_stamp < $totalAmount) {
//                        throw new ResourceException('粮票余额不足');
//                    }
//                    $card_no = $user->master_card_no;
//                    $user_food_stamp_description = '购买-' . $user_food_stamp_description;
//                    $user_food_stamp_action_type = UserFoodStamp::ACTION_TYPE_ORDER_PAY;
//                }
//                $user_food_stamp_description = substr($user_food_stamp_description, 0, -1);
//                $res = (new UserFoodStampService())->useFoodStamp($user_food_stamp_action_type, $user, $totalAmount, $card_no, $user_food_stamp_description, $order);
//                if (!$res) {
//                    throw new ResourceException('粮票支付失败');
//                }
//                $order->update([
//                    'payment_method' => 'food_stamp',
//                    'paid_at'        => Carbon::now()
//                ]);
//            }

            // 未选择收货地址导入礼品库
            if (empty($address)) {
                (new UserGiftService())->orderToGift($order->id);
            }

            event(new OrderPaid($order));
            // 将下单的商品从购物车中移除
            $skuIds = collect($items)->pluck('sku_id')->all();
            app(CartService::class)->remove($skuIds);
            return $order;
        });

        // 发帖
        //(new PostService())->add($user, $order);
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
            $sku_price = $sku->price;
            if ($user->level == 2) {
                $sku_price = $sku->club_tax_price;
            };
            $discount_amount = sprintf("%.2f", ($sku_price * $amount) - $totalAmount);
            // 创建一个订单
            $order = new Order([
                'address'      => [ // 将地址信息放入订单中
                    'address'       => $address->full_address,
                    'zip'           => $address->zip,
                    'contact_name'  => $address->contact_name,
                    'contact_phone' => $address->contact_phone,
                ],
                'remark'       => $remark,
                'total_amount' => $totalAmount,
                'type'         => Order::TYPE_GROUP,
            ]);
            // 订单关联到当前用户
            $order->user()->associate($user);
            // 写入数据库
            $order->save();
            // 创建一个新的订单项并与 SKU 关联
            $item = $order->items()->make([
                'amount' => $amount,
                'price'  => $sku->product->group->price,
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
                    'used'    => 1,
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
                    'order_id'     => $order->id,
                    'product_id'   => $sku->product->id,
                    'sku_id'       => $sku->id,
                    'target_count' => $sku->product->group->target_count,
                    'user_count'   => 1,
                    'status'       => Group::STATUS_PENDING,
                ]);
                // 团购关联到当前用户
                $group->user()->associate($user);
                // 写入数据库
                $group->save();
            }

            $group_item = new GroupItem([
                'group_id'   => $group->id,
                'order_id'   => $order->id,
                'product_id' => $sku->product->id,
                'sku_id'     => $sku->id,
                'is_head'    => $is_head,
                'status'     => GroupItem::STATUS_PENDING,
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

    public function bargain(User $user, UserAddress $address, ProductSku $sku, $amount, CouponCode $coupon = null, Bargain $bargain = null, $remark, $identity)
    {
        // 开启事务
        $order = \DB::transaction(function () use ($amount, $sku, $user, $address, $coupon, $bargain, $remark, $identity) {
            // 更新地址最后使用时间
            $address->update(['last_used_at' => Carbon::now()]);
            $totalAmount = $bargain->current_price * $amount;

            $tax_price = $bargain->current_price;
            $sku_tax_rate = $sku->tax_rate;
            $sku_price = sprintf("%.2f", ($tax_price / (1 + $sku_tax_rate / 100)));
            $tax = sprintf("%.2f", ($tax_price - $sku_price));
            // 成本价
            $cost = $sku->club_tax_price;
            // 利润
            $profit = $bargain->current_price - $cost;
            if ($profit < 0) {
                //throw new ResourceException('销售价格不能低于最低售价');
            }
            $warehouse_id = 0;
            if ($sku->product->warehouse_id) {
                $warehouse_id = $sku->product->warehouse_id;
            };

            // 创建一个订单
            $order = new Order([
                'address'      => [ // 将地址信息放入订单中
                    'id'            => $address->id,
                    'province'      => $address->province,
                    'city'          => $address->city,
                    'district'      => $address->district,
                    'address'       => $address->address,
                    'full_address'  => $address->full_address,
                    'zip'           => $address->zip,
                    'contact_name'  => $address->contact_name,
                    'contact_phone' => $address->contact_phone,
                    'real_name'     => $address->real_name,
                    'idcard_no'     => $address->idcard_no,
                ],
                'identity'     => [ // 将证件信息放入订单中
                    'id'           => $identity ? $identity->id : '',
                    'real_name'    => $identity ? $identity->real_name : '',
                    'phone'        => $identity ? $identity->phone : '',
                    'idcard_no'    => $identity ? $identity->idcard_no : '',
                    'idcard_front' => !empty($identity->idcard_front) ? config('api.img_host') . $identity->idcard_front : '',
                    'idcard_back'  => !empty($identity->idcard_back) ? config('api.img_host') . $identity->idcard_back : '',
                ],
                'remark'       => $remark,
                'total_amount' => 0,
                'type'         => Order::TYPE_BARGAIN,
                'warehouse_id' => $warehouse_id,
            ]);
            // 订单关联到当前用户
            $order->user()->associate($user);
            // 写入数据库
            $order->save();
            // 创建一个新的订单项并与 SKU 关联
            $item = $order->items()->make([
                'amount'   => $amount,
                'qty'      => $sku->qty,
                'price'    => $sku_price,
                'tax_rate' => $sku_tax_rate,
                'tax'      => $tax,
                'cost'     => $cost,
                'profit'   => $profit
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
                    'used'    => 1,
                    'used_at' => Carbon::now(),
                ]);
            }
            // 更新订单总金额
            $totalAmount = sprintf("%.2f", $totalAmount);
            $order->update(['total_amount'   => $totalAmount,
                            'product_amount' => $totalAmount,
                            'tax_amount'     => $tax,
                            'cost_amount'    => $cost,
                            'profit_amount'  => $profit]);
            // 创建开团信息
            $bargain->update(['order_id' => $order->id]);
            return $order;
        });

        // 剩余秒数与默认订单关闭时间取较小值作为订单关闭时间
        dispatch(new CloseOrder($order, config('app.order_ttl')));

        return $order;
    }


    public function bargain_old(User $user, UserAddress $address, ProductSku $sku, $amount, CouponCode $coupon = null, Bargain $bargain = null, $remark)
    {

        // 开启事务
        $order = \DB::transaction(function () use ($amount, $sku, $user, $address, $coupon, $bargain, $remark) {
            // 更新地址最后使用时间
            $address->update(['last_used_at' => Carbon::now()]);
            $totalAmount = $bargain->current_price * $amount;

            $sku_price = $sku->tax_price;
            if ($user->level == 2 && $user->shop_id == 1001) {
                $sku_price = $sku->club_tax_price;
            };
            $discount_amount = sprintf("%.2f", ($sku_price * $amount) - $totalAmount);
            // 创建一个订单
            $order = new Order([
                'address'         => [ // 将地址信息放入订单中
                    'address'       => $address->full_address,
                    'zip'           => $address->zip,
                    'contact_name'  => $address->contact_name,
                    'contact_phone' => $address->contact_phone,
                ],
                'remark'          => $remark,
                'total_amount'    => $totalAmount,
                'discount_amount' => $discount_amount,
                'type'            => Order::TYPE_BARGAIN,
            ]);
            // 订单关联到当前用户
            $order->user()->associate($user);
            // 写入数据库
            $order->save();
            // 创建一个新的订单项并与 SKU 关联
            $item = $order->items()->make([
                'amount' => $amount,
                'price'  => $bargain->current_price,
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
                    'used'    => 1,
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
//                $order->total_amount = 0.01;
                app('wechat_pay')->refund([
                    'out_trade_no'  => $order->no . '_' . $order->suffix,
                    'total_fee'     => $order->total_amount * 100,
                    'refund_fee'    => $order->total_amount * 100,
                    'out_refund_no' => $refundNo,
                    'notify_url'    => app('Dingo\Api\Routing\UrlGenerator')->version('v1')->route('payment.wechat.refund_notify'),
                ]);
                $order->update([
                    'closed'        => true,
                    'refund_no'     => $refundNo,
                    'refund_status' => Order::REFUND_STATUS_PROCESSING,
                ]);
                break;
            case 'abcpay':
                // 生成退款订单号
                $refundNo = Order::getAvailableRefundNo();
                // 更新退款状态为退款中
                $order->update([
                    'closed'        => true,
                    'refund_no'     => $refundNo,
                    'refund_status' => Order::REFUND_STATUS_PROCESSING,
                ]);
//                $order->total_amount = 0.01;
                $refundData = [
                    'out_trade_no'  => $order->no . '_' . $order->suffix,
                    'total_fee'     => $order->total_amount,
                    'refund_fee'    => $order->total_amount,
                    'out_refund_no' => $refundNo
                ];
                $res = (new AbcpayService())->refund($refundData);
                if ($res['return_code'] === '0000') {
                    $order->update([
                        'refund_status' => Order::REFUND_STATUS_SUCCESS,
                        'refund_no'     => $refundNo,
                    ]);
                } else {
                    $order->update([
                        'refund_status' => Order::REFUND_STATUS_FAILED,
                    ]);
                    throw new ResourceException('退款失败 return_code：' . $res['return_code'] . ' return_msg：' . $res['return_msg']);
                }
                break;
            case 'allinpay':
                // 生成退款订单号
                $refundNo = Order::getAvailableRefundNo();
                $no = !empty($order->suffix) ? $order->no . '_' . $order->suffix : $order->no;

                // 拆单订单
                if ($order->parent_id) {
                    $parent_order = Order::find($order->parent_id);
                    if ($parent_order) {
                        $no = !empty($parent_order->suffix) ? $parent_order->no . '_' . $parent_order->suffix : $parent_order->no;
                    }
                }
                if ($order->paid_at->isToday() && !$order->parent_id) {
                    $res = (new AllinpayService())->cancel([
                        'out_trade_no'  => $no,
                        'total_fee'     => $order->total_amount * 100,
                        'out_refund_no' => $refundNo,
                    ]);
                } else {
                    $res = (new AllinpayService())->refund([
                        'out_trade_no'  => $no,
                        'total_fee'     => $order->total_amount * 100,
                        'out_refund_no' => $refundNo,
                    ]);
                }
                if ($res['trxstatus'] == '0000') {
                    //更新退款状态
                    $order->update([
                        'closed'        => true,
                        'refund_no'     => $refundNo,
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
                'address'      => [ // address 字段直接从 $addressData 数组中读取
                    'address'       => $addressData['province'] . $addressData['city'] . $addressData['district'] . $addressData['address'],
                    'zip'           => $addressData['zip'],
                    'contact_name'  => $addressData['contact_name'],
                    'contact_phone' => $addressData['contact_phone'],
                ],
                'remark'       => '',
                'total_amount' => $sku->price,
                'type'         => Order::TYPE_SECKILL,
            ]);
            // 订单关联到当前用户
            $order->user()->associate($user);
            // 写入数据库
            $order->save();
            // 创建一个新的订单项并与 SKU 关联
            $item = $order->items()->make([
                'amount' => 1, // 秒杀商品只能一份
                'price'  => $sku->price,
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

    public function exchange(User $user, $items, $address, $delivery_method = '', $remark = '')
    {
        // 开启一个数据库事务
        $order = \DB::transaction(function () use ($user, $items, $address, $delivery_method, $remark) {
            // 更新此地址的最后使用时间
            !$address ?: $address->update(['last_used_at' => Carbon::now()]);
            // 创建一个订单
            $order = new Order([
                'address'         => [ // 将地址信息放入订单中
                    'id'            => $address->id ?? '',
                    'province'      => $address->province ?? '',
                    'city'          => $address->city ?? '',
                    'district'      => $address->district ?? '',
                    'address'       => $address->address ?? '',
                    'full_address'  => $address->full_address ?? '',
                    'zip'           => $address->zip ?? '',
                    'contact_name'  => $address->contact_name ?? '',
                    'contact_phone' => $address->contact_phone ?? '',
                    'real_name'     => $address->real_name ?? '',
                    'idcard_no'     => $address->idcard_no ?? '',
                ],
                'remark'          => $remark,
                'delivery_method' => $delivery_method,
                'total_amount'    => 0,
                'type'            => Order::TYPE_EXCHANGE,
            ]);
            // 订单关联到当前用户
            $order->user()->associate($user);
            // 写入数据库
            $order->save();
            $totalAmount = 0;
            $taxAmount = 0;
            $warehouse_id = 0;
            $cost_amount = 0;
            $profit_amount = 0;
            $supplier_id = 0;
            // 遍历用户提交的 SKU
            foreach ($items as $k => $data) {
                if ($data['amount'] <= 0) {
                    throw new ResourceException('兑换数量不能小于等于0');
                }
                $user_gift = UserGift::where('product_sku_id', $data['sku_id'])
                    ->where('user_id', $user->id)
                    ->where('shop_id', $user->shop_id)
                    ->lockForUpdate()
                    ->first();
                if (!$user_gift) {
                    throw new ResourceException('礼品不存在');
                }
                if ($user_gift->count < $data['amount']) {
                    throw new ResourceException('礼品数不足');
                }
                // 礼品扣减
                $sku = ProductSku::with('product')->find($data['sku_id']);
                $rt = (new UserGiftService())->outStock(UserGiftItem::USE_METHOD_EXCHANGE, $user, $sku, $data['amount'], $user_gift->sku_price, $order, null);
                if (!$rt) {
                    throw new ResourceException('礼品扣减失败');
                }
                // 兑换积分
                if ($delivery_method === Order::DELIVERY_METHOD_INTEGRAL && $sku->integral > 0) {
                    //$description = $sku->title . ' * ' . $data['amount'];
                    $description = '订单积分兑换';
                    (new UserIntegralService())->getIntegral(UserIntegral::GET_METHOD_ORDER_EXCHANGE, $user, $sku->integral * $data['amount'], $description, $order, $sku);
                }
                // 兑换粮票
                if ($delivery_method === Order::DELIVERY_METHOD_FOOD_STAMP && $sku->integral > 0) {
                    //$description = $sku->title . ' * ' . $data['amount'];
                    $description = '礼品兑换-' . $sku->title . ' * ' . $data['amount'];
                    (new UserFoodStampService())->getFoodStamp(UserFoodStamp::ACTION_TYPE_ORDER_EXCHANGE, $order->user, $sku->integral * $data['amount'], $description, $order, $order->user->master_card_no);
                }
                // 仓库
                if ($sku->product->warehouse_id) {
                    $warehouse_id = $sku->product->warehouse_id;
                };
                // 供应商
                if ($sku->product->supplier_id) {
                    $supplier_id = $sku->product->supplier_id;
                };
                // 成本价
                $cost = $sku->min_price;
                // 利润
                $profit = $sku->price - $sku->min_price;
                if ($profit < 0) {
//                    throw new ResourceException('销售价格不能低于最低售价');
                }
                // 创建一个 OrderItem 并直接与当前订单关联
                $item = $order->items()->make([
                    'amount'         => $data['amount'],
                    'qty'            => $sku->qty,
                    'price'          => $sku->price,
                    'cost'           => $cost,
                    'profit'         => $profit,
                    'tax'            => 0,
                    'tax_rate'       => 0,
                    'sku_title'      => $sku->title,
                    'sku_image'      => $sku->product->cover ?? '',
                    'sku_price'      => $sku->price,
                    'sku_integral'   => $sku->integral,
                    'sku_food_stamp' => $sku->integral,
                ]);
                // 成本总额
                $cost_amount += $cost * $data['amount'];
                // 利润总额
                $profit_amount += $profit * $data['amount'];
                $item->product()->associate($sku->product_id);
                $item->productSku()->associate($sku);
                $item->save();
                // 虚拟商品获取卡密
                if ($sku->is_virtual) {
                    $sku_id = $sku->id;
                    $sku_cards = ProductSkuCard::where('product_sku_id', $sku_id)
                        ->where('activated', 1)
                        ->where('exchange', 0)
                        ->where('used', 0)
                        ->lockForUpdate()
                        ->take($data['amount'])
                        ->get();
                    if ($sku_cards->count() < $data['amount']) {
                        throw new ResourceException('卡密库存不足');
                    }
                    foreach ($sku_cards as $card) {
                        $card->user_id = $user->id;
                        $card->order_id = $order->id;
                        $card->exchange = 1;
                        $card->exchange_at = Carbon::now();
                        $card->save();
                    }
                }
                $totalAmount += $sku->price * $data['amount'];
                if ($data['amount'] <= 0) {
                    throw new ResourceException('减库存不可小于0');
                }

            }
            // 生成二维码 条形码
            $disk = Storage::disk('cosv5');
            $base64_img = 'data:image/png;base64,' . DNS1D::getBarcodePNG($order->uuid, "C39", 1, 33);
            $filename = 'images/barcode/' . str_random(32) . '.jpg';
            preg_match('/^(data:\s*image\/(\w+);base64,)/', $base64_img, $res);
            $base64_img = base64_decode(str_replace($res[1], '', $base64_img));
            $res = $disk->put($filename, $base64_img);//第一个参数是你储存桶里想要放置文件的路径，第二个参数是文件对象
            $bar_code = $res ? $filename : '';//获取到文件的线上地址

            $base64_img = 'data:image/png;base64,' . DNS2D::getBarcodePNG($order->uuid, "QRCODE", 30, 30);
            $filename = 'images/qrcode/' . str_random(32) . '.jpg';
            preg_match('/^(data:\s*image\/(\w+);base64,)/', $base64_img, $res);
            $base64_img = base64_decode(str_replace($res[1], '', $base64_img));
            $res = $disk->put($filename, $base64_img);//第一个参数是你储存桶里想要放置文件的路径，第二个参数是文件对象
            $qr_code = $res ? $filename : '';//获取到文件的线上地址

            // 商品总价
            $product_amount = sprintf("%.2f", $totalAmount);
            $totalAmount = 0;
            // 更新订单总金额
            $order->update(['total_amount'   => $totalAmount,
                            'product_amount' => $product_amount,
                            'tax_amount'     => $taxAmount,
                            'cost_amount'    => $cost_amount,
                            'profit_amount'  => $profit_amount,
                            'warehouse_id'   => $warehouse_id,
                            'supplier_id'    => $supplier_id,
                            'qr_code'        => $qr_code,
                            'bar_code'       => $bar_code,
            ]);
            // 支付金额0直接支付完成
            if ($totalAmount == 0) {
                $order->update([
                    'paid_at' => Carbon::now()
                ]);
                //event(new OrderPaid($order));
            }
            if ($delivery_method === Order::DELIVERY_METHOD_INTEGRAL || $delivery_method === Order::DELIVERY_METHOD_FOOD_STAMP) {
                $order->update([
                    'delivered_at' => Carbon::now()
                ]);
            }
            return $order;
        });
        //dispatch(new CloseOrder($order, config('app.order_ttl')));

        return $order;
    }

    public function writeOff(Order $order, SupplierStore $store)
    {
        if (!$order || !$order->items) {
            throw new ResourceException('订单数据异常');
        }
        // 开启事务
        $order = \DB::transaction(function () use ($order, $store) {
            foreach ($order->items as $item) {
                if (!$item->product) {
                    throw new ResourceException('订单数据异常');
                }
                if (!$item->product->supplier_id) {
                    throw new ResourceException('商品未关联供货商');
                }
                if ($item->product->supplier_id != $store->supplier_id) {
                    throw new ResourceException('订单商品和供货商不匹配');
                }
            }
            $order->received_at = Carbon::now();
            $order->save();
            // 核销明细
            $writeOffLog = new WriteOffLog([
                'supplier_store_id' => $store->id,
                'order_id'          => $order->id,
                'shop_id'           => $order->shop_id,
            ]);
            // 写入数据库
            $writeOffLog->save();

            return $order;
        });
        return $order;
    }

    public function recharge(User $user, $food_stamp_set, $remark = '')
    {
        // 开启事务
        $order = \DB::transaction(function () use ($user, $food_stamp_set, $remark) {
            if ($food_stamp_set->food_stamp_amount <= 0) {
                throw new ResourceException('充值金额不能小于等于0');
            }

            // 创建一个订单
            $order = new Order([
                'address'           => [
                    'id'            => $address->id ?? '',
                    'province'      => $address->province ?? '',
                    'city'          => $address->city ?? '',
                    'district'      => $address->district ?? '',
                    'address'       => $address->address ?? '',
                    'full_address'  => $address->full_address ?? '',
                    'zip'           => $address->zip ?? '',
                    'contact_name'  => $address->contact_name ?? '',
                    'contact_phone' => $address->contact_phone ?? '',
                    'real_name'     => $address->real_name ?? '',
                    'idcard_no'     => $address->idcard_no ?? '',
                ],
                'remark'            => $remark,
                'total_amount'      => $food_stamp_set->food_stamp_amount,
                'food_stamp_set_id' => $food_stamp_set->id,
                'type'              => Order::TYPE_RECHARGE,
            ]);
            // 订单关联到当前用户
            $order->user()->associate($user);
            // 写入数据库
            $order->save();
            return $order;
        });

        // 剩余秒数与默认订单关闭时间取较小值作为订单关闭时间
        dispatch(new CloseOrder($order, config('app.order_ttl')));

        return $order;
    }
}
