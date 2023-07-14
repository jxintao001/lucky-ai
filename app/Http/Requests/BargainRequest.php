<?php

namespace App\Http\Requests;

use App\Models\Product;
use Carbon\Carbon;
use Illuminate\Validation\Rule;
use App\Models\ProductSku;

class BargainRequest extends Request
{
    public function rules()
    {
        return [
            'sku_id' => [ // 检查 items 数组下每一个子数组的 sku_id 参数
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
                    if ($sku->product->type !== Product::TYPE_BARGAIN) {
                        $fail('该商品不支持砍价');
                        return;
                    }
                    if ($sku->product->bargain->begin_at && $sku->product->bargain->begin_at->gt(Carbon::now())) {
                        $fail('该商品砍价未开始');
                        return;
                    }
                    if ($sku->product->bargain->end_at && $sku->product->bargain->end_at->lt(Carbon::now())) {
                        $fail('该商品砍价已结束');
                        return;
                    }
                    if ($sku->stock === 0) {
                        $fail('该商品已售完');
                        return;
                    }
                },
            ]
        ];
    }

    public function attributes()
    {
        return [
            'sku_id'    => '商品sku'
        ];
    }

    public function messages()
    {
        return [
            'sku_id.required' => '请选择商品'
        ];
    }
}
