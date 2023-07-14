<?php

namespace App\Http\Requests;

use App\Models\Product;
use Carbon\Carbon;
use Illuminate\Validation\Rule;
use App\Models\ProductSku;

class GroupOrderRequest extends Request
{
    public function rules()
    {
        return [
            // 判断用户提交的地址 ID 是否存在于数据库并且属于当前用户
            // 后面这个条件非常重要，否则恶意用户可以用不同的地址 ID 不断提交订单来遍历出平台所有用户的收货地址
            'address_id'     => ['required', Rule::exists('user_addresses', 'id')->where('user_id', $this->user()->id)],
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
                    // 团购商品下单接口仅支持团购商品的 SKU
                    if ($sku->product->type !== Product::TYPE_GROUP) {
                        $fail('该商品不支持团购');
                        return;
                    }
                    // 还需要判断众筹本身的状态，如果不是众筹中则无法下单
                    if ($sku->product->group->begin_at && $sku->product->group->begin_at->gt(Carbon::now())) {
                        $fail('该商品团购未开始');
                        return;
                    }
                    if ($sku->product->group->end_at && $sku->product->group->end_at->lt(Carbon::now())) {
                        $fail('该商品团购已结束');
                        return;
                    }
                    if ($sku->stock === 0) {
                        $fail('该商品已售完');
                        return;
                    }
                    if ($this->input('amount') > 0 && $sku->stock < $this->input('amount')) {
                        $fail('该商品库存不足');
                        return;
                    }
                },
            ],
            'amount' => ['required', 'integer', 'min:1'],
        ];
    }

    public function attributes()
    {
        return [
            'address_id'        => '地址id',
            'sku_id'    => '商品sku_id',
            'amount'    => '商品数量',
        ];
    }
}
