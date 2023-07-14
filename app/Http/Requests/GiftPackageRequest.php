<?php

namespace App\Http\Requests;

use App\Models\GiftPackage;

class GiftPackageRequest extends Request
{
    public function rules()
    {
        return [
            'type'           => ['required',
                function ($attribute, $value, $fail) {
                    if (!in_array($value, array_flip(GiftPackage::$typeMap))) {
                        $fail('type 无效');
                        return;
                    }
                    if ($value === GiftPackage::TYPE_MANY_PEOPLE_SET && $this->input('set_amount') < 1) {
                        $fail('多人整套 set_amount 不能小于1');
                        return;
                    }

                }
            ],
            //'title'          => 'required',
//            'question'       => 'required',
//            'answer'         => 'required',
            'set_amount'     => ['integer', 'min:1'],
            'receive_limit'  => ['integer', 'min:1'],
            'items'          => ['required', 'array'],
            'items.*.sku_id' => [ // 检查 items 数组下每一个子数组的 sku_id 参数
                'required',
                function ($attribute, $value, $fail) {
                    // == todo == 验证
//                    if (!$sku = ProductSku::find($value)) {
//                        $fail('该商品不存在');
//                        return;
//                    }
//
//                    if (in_array($sku->product_id, \Auth::user()->shop->banned_products)) {
//                        $fail('该商品禁止销售');
//                        return;
//                    }
//                    if (!$sku->product->on_sale) {
//                        $fail('该商品未上架');
//                        return;
//                    }
//                    if (($sku->is_presale === false && $sku->stock === 0) || ($sku->is_presale === true && $sku->presale === 0)) {
//                        $fail('该商品已售完');
//                        return;
//                    }
//                    // 获取当前索引
//                    preg_match('/items\.(\d+)\.sku_id/', $attribute, $m);
//                    $index = $m[1];
//                    // 根据索引找到用户所提交的购买数量
//                    $amount = $this->input('items')[$index]['amount'];
//                    // 是否为预售商品
//                    if ($sku->is_presale) {
//                        if ($amount > 0 && $amount > $sku->presale) {
//                            $fail('该商品库存不足');
//                            return;
//                        }
//                    } else {
//                        if ($amount > 0 && $amount > $sku->stock) {
//                            $fail('该商品库存不足');
//                            return;
//                        }
//                    }
                },
            ],
            'items.*.amount' => ['required', 'integer', 'min:1'],
        ];
    }

    public function attributes()
    {
        return [
            'address_id'     => '地址id',
            'items'          => '礼品列表',
            'items.*.sku_id' => '礼品sku_id',
            'items.*.amount' => '礼品数量',
        ];
    }
}
