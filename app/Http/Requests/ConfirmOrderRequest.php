<?php

namespace App\Http\Requests;

use Illuminate\Validation\Rule;
use App\Models\ProductSku;

class ConfirmOrderRequest extends Request
{
    public function rules()
    {
        return [
            'items'          => ['required', 'array'],
            'items.*.sku_id' => [ // 检查 items 数组下每一个子数组的 sku_id 参数
                'required',
                function ($attribute, $value, $fail) {
                    if (!$sku = ProductSku::find($value)) {
                        $fail('该商品不存在');
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
                    // 获取当前索引
                    preg_match('/items\.(\d+)\.sku_id/', $attribute, $m);
                    $index  = $m[1];
                    // 根据索引找到用户所提交的购买数量
                    $amount = $this->input('items')[$index]['amount'];
                    // 是否为预售商品
                    if ($sku->is_presale){
                        if ($amount > 0 && $amount > $sku->presale) {
                            $fail('该商品库存不足');
                            return;
                        }
                    }else{
                        if ($amount > 0 && $amount > $sku->stock) {
                            $fail('该商品库存不足');
                            return;
                        }
                    }

                },
            ],
            'items.*.amount' => ['required', 'integer', 'min:1'],
        ];
    }

    public function attributes()
    {
        return [
            'items'             => '商品列表',
            'items.*.sku_id'    => '商品sku_id',
            'items.*.amount'    => '商品数量',
        ];
    }
}
