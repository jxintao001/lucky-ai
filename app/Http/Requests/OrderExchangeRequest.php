<?php

namespace App\Http\Requests;

use App\Models\Order;
use App\Models\ProductSku;
use App\Models\UserAddress;

class OrderExchangeRequest extends Request
{
    public function rules()
    {
        return [
            'items'           => ['required', 'array'],
            'items.*.sku_id'  => [ // 检查 items 数组下每一个子数组的 sku_id 参数
                'required',
                function ($attribute, $value, $fail) {
                    if (count($this->input('items')) > 1) {
                        $fail('只支持单个sku兑换');
                        return;
                    }
                    if (!$sku = ProductSku::find($value)) {
                        $fail('该商品不存在');
                        return;
                    }
                    if (in_array($sku->product_id, \Auth::user()->shop->banned_products)) {
                        $fail('该商品禁止销售');
                        return;
                    }
                    if (!$sku->product->on_sale) {
                        $fail('该商品未上架');
                        return;
                    }
                    if (($sku->is_presale === false && $sku->stock === 0) || ($sku->is_presale === true && $sku->presale === 0)) {
                        $fail('该商品已售完');
                        return;
                    }
                    if ($this->input('delivery_method') === Order::DELIVERY_METHOD_EXPRESS) {
                        if (!$this->input('address_id')) {
                            $fail('收件地址不能为空');
                            return;
                        }
                        $user_address = UserAddress::where('id', $this->input('address_id'))->where('user_id', $this->user()->id)->first();
                        if (!$user_address) {
                            $fail('收件地址不存在');
                            return;
                        }
                    }
                    if (!in_array($this->input('delivery_method'), [Order::DELIVERY_METHOD_INTEGRAL, Order::DELIVERY_METHOD_FOOD_STAMP])) {
                        if ($sku->type === ProductSku::TYPE_ONLINE && $this->input('delivery_method') !== Order::DELIVERY_METHOD_EXPRESS) {
                            $fail('配送方式选择错误');
                            return;
                        }
                        if ($sku->type === ProductSku::TYPE_OFFLINE && $this->input('delivery_method') !== Order::DELIVERY_METHOD_PICK_UP) {
                            $fail('配送方式选择错误');
                            return;
                        }
                    }
                    // 获取当前索引
                    preg_match('/items\.(\d+)\.sku_id/', $attribute, $m);
                    $index = $m[1];
                    // 根据索引找到用户所提交的购买数量
                    $amount = $this->input('items')[$index]['amount'];
                    // 是否为预售商品
                    if ($sku->is_presale) {
                        if ($amount > 0 && $amount > $sku->presale) {
                            $fail('该商品库存不足');
                            return;
                        }
                    } else {
                        if ($amount > 0 && $amount > $sku->stock) {
                            $fail('该商品库存不足');
                            return;
                        }
                    }
                },
            ],
            'items.*.amount'  => ['required', 'integer', 'min:1'],
            'delivery_method' => ['required',
                function ($attribute, $value, $fail) {
                    if (!in_array($value, [Order::DELIVERY_METHOD_EXPRESS, Order::DELIVERY_METHOD_PICK_UP, Order::DELIVERY_METHOD_INTEGRAL, Order::DELIVERY_METHOD_FOOD_STAMP])) {
                        $fail('配送方式类型错误');
                        return;
                    }
                }
            ],
        ];
    }

    public function attributes()
    {
        return [
            'address_id'     => '地址id',
            'items'          => '商品sku',
            'items.*.sku_id' => '商品sku_id',
            'items.*.amount' => '商品数量',
        ];
    }
}
