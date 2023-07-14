<?php

namespace App\Services;


use App\Models\CombinationProduct;
use App\Models\Order;
use App\Models\UserAddress;
use App\Models\FreightRule;
use App\Models\Product;
use App\Models\Warehouse;
use App\User;

class SplitOrderService
{
    protected $order_items;
    protected $warehouses;

    public function __construct()
    {
        $this->warehouses = $this->setWarehouses();
    }

    public function splitWarehouse()
    {
        $items = [];
        foreach ($this->warehouses as $k => $warehouse) {
            foreach ($this->order_items as $k2 => $item) {
                if ($item->product->warehouse->id == $warehouse->id) {
                    $items[$warehouse->id][] = $item;
                }
            }
        }
        return $items;
    }

//    public function getTotalFreight($items)
//    {
//        $total_freight = 0;
//        foreach ($items as $k => $item) {
//            if ($item['product']['freight'] > $total_freight) {
//                $total_freight = $item['product']['freight'];
//            }
//        }
//        $total_freight = sprintf("%.2f", $total_freight);
//        return $total_freight;
//    }
    
    public function getTotalFreight($items, $total_weight, $address_id)
    {
        $total_freight = 0;
        $address = UserAddress::find($address_id);
        $province = $address->province ?? '';
        $freight_rule = FreightRule::where('area', $province)->where('shop_id', 1)->first();
        if($freight_rule){
            $total_freight = $freight_rule->first_weight_price;
            $total_weight = $total_weight + 0.3;
            if($total_weight <= 1){
                $total_weight = 1;
            }
            $continue_weight = ceil(($total_weight - 1) / 1);
            $continue_weight_price = $continue_weight * $freight_rule->continue_weight_price;
            $total_freight += $continue_weight_price;
        }
        return $total_freight;
    }

    public function getTotalTaxAmount($items)
    {
        $taxAmount = 0;
        foreach ($items as $k => $item) {
            // 组合商品
            if ($item['product']['type'] == Product::TYPE_COMBINATION) {
                $cps = CombinationProduct::where('product_id', $item['product_id'])->get();
                if (!$cps) {
                    continue;
                }
                foreach ($cps as $cp) {
                    if (auth('api')->user()->level > 1){
                        $taxAmount += $this->calculateTax($cp['club_price'], $cp['club_tax_rate']) * $item['amount'] * $cp['amount'];
                    }else{
                        $taxAmount += $this->calculateTax($cp['price'], $cp['tax_rate']) * $item['amount'] * $cp['amount'];
                    }
                    
                }
            } else {
                $taxAmount += $this->calculateTax($item['price'], $item['tax_rate']) * $item['amount'];
            }
        }
        
        return sprintf("%.2f", $taxAmount);
    }

    public function calculateTax($price, $tax_rate)
    {
        return number_format($price * ($tax_rate / 100), 2, '.', '');
    }

    public function setWarehouses()
    {
        $warehouses = Warehouse::where('is_banned', 0)->get();
        return $warehouses;

    }

    public function setOrderItems($order)
    {
        $this->order_items = $order->items;
        return $this;
    }

    public function getSplitOrderItems($order)
    {
        return $this->setOrderItems($order)->splitWarehouse();
    }


    public function split($order)
    {
        // 是否已经拆分
        if ($order->split) {
            return true;
        }
        // 订单拆分
        $split_items = $this->getSplitOrderItems($order);
        if(count($split_items) <= 1){
            return true;
        }
        // 创建子订单
        $split_number = 1;
        foreach ($split_items as $item) {
            $this->makeSplitOrder($order, $item, $split_number);
            $split_number ++;
        }
        // 标记为已拆单
        $order->update(['split' => true]);
    }

    public function makeSplitOrder($porder, $pitems, $split_number = 1)
    {
        // 开启一个数据库事务
        \DB::transaction(function () use ($porder, $pitems, $split_number) {
            // 创建一个订单
            $order = new Order([
                'type' => $porder->type,
                'suffix' => $porder->suffix,
                'user_id' => $porder->user_id,
                'address' => $porder->address,
                'identity' => $porder->identity,
                'freight' => 0,
                'product_amount' => 0,
                'total_amount' => 0,
                'cost_amount' => 0,
                'profit_amount' => 0,
                'coupon_amount' => 0,
                'discount_amount' => 0,
                'tax_amount' => 0,
                'warehouse_id' => 0,
                'remark' => $porder->remark,
                'seller_remark' => $porder->seller_remark,
                'paid_at' => $porder->paid_at,
                'coupon_code_id' => $porder->coupon_code_id,
                'group_id' => $porder->group_id,
                'bargain_id' => $porder->bargain_id,
                'payment_method' => $porder->payment_method,
                'payment_no' => $porder->payment_no,
                'refund_status' => $porder->refund_status,
                'refund_no' => $porder->refund_no,
                'closed' => $porder->closed,
                'reviewed' => $porder->reviewed,
                'ship_status' => $porder->ship_status,
                'ship_data' => $porder->ship_data,
                'waybill_print' => $porder->waybill_print,
                'customs_status' => $porder->customs_status,
                'customs_data' => $porder->customs_data,
                'pay_data' => $porder->pay_data,
                'pay_notify_data' => $porder->pay_notify_data,
                'extra' => $porder->extra,
                'parent_id' => $porder->id,
                'split' => 0,
                'split_number' => $split_number,
                'delivered_at' => $porder->delivered_at,
                'received_at' => $porder->received_at,
                'created_at' => $porder->created_at,
                'updated_at' => $porder->updated_at,
                'deleted_at' => $porder->deleted_at,
                'shop_id' => $porder->shop_id,
            ]);
            // 写入数据库
            $order->save();
            $freight = 0;
            $product_amount = 0;
            $cost_amount = 0;
            $profit_amount = 0;
            $coupon_amount = 0;
            $discount_amount = 0;
            $tax_amount = 0;
            $total_amount = 0;
            $warehouse_id = 0;

            foreach ($pitems as $k => $pitem) {
                if ($pitem->product->warehouse_id) {
                    $warehouse_id = $pitem->product->warehouse_id;
                };
                // 创建一个 OrderItem 并直接与当前订单关联
                $item = $order->items()->make([
                    'cp_id' => $pitem->cp_id,
                    'amount' => $pitem->amount,
                    'qty' => $pitem->qty,
                    'price' => $pitem->price,
                    'coupon' => $pitem->coupon,
                    'coupon_amount' => $pitem->coupon_amount,
                    'tax_rate' => $pitem->tax_rate,
                    'tax' => $pitem->tax,
                    'cost' => $pitem->cost,
                    'profit' => $pitem->profit,
                    'rating' => $pitem->rating,
                    'review' => $pitem->review,
                    'reviewed_at' => $pitem->reviewed_at,
                    'shop_id' => $porder->shop_id,
                ]);
                $item->product()->associate($pitem->product);
                $item->productSku()->associate($pitem->productSku);
                $item->save();
                // 成本总额
                $cost_amount += $pitem->cost * $pitem->amount;
                // 利润总额
                $profit_amount += $pitem->profit * $pitem->amount;
                // 商品总价
                $product_amount += $pitem->price * $pitem->amount;
                // 优惠总额
                $coupon_amount += $pitem->coupon_amount;
                // 税额
                $tax_amount += $pitem->tax * $pitem->amount;
            }
            // 商品总价
            $product_amount = sprintf("%.2f", $product_amount);
            $total_amount = sprintf("%.2f", $product_amount + $freight + $tax_amount - $coupon_amount);
            // 更新订单总金额
            $order->update([
                'freight' => $freight,
                'product_amount' => $product_amount,
                'cost_amount' => $cost_amount,
                'profit_amount' => $profit_amount,
                'coupon_amount' => $coupon_amount,
                'tax_amount' => $tax_amount,
                'total_amount' => $total_amount,
                'warehouse_id' => $warehouse_id,
            ]);
            return $order;
        });

    }

}
