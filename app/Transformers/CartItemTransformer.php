<?php

namespace App\Transformers;

class CartItemTransformer extends BaseTransformer
{
    protected $availableIncludes = ['product'];

    public function transformData($model)
    {
//        $buy['buy_price'] = $model->tax_price;
//        if (auth('api')->user()) {
//            if (auth('api')->user()->level > 1) {
//                $buy['buy_price'] = $model->club_tax_price;
//            }
//        }
        return [
            'id'             => $model->id,
            'title'          => $model->title,
            'description'    => $model->description,
            'original_price' => foodStampValue($model->original_price),
//            'price'          => foodStampValue($buy['buy_price']),
            'price'          => $model->tax_price,
            'stock'          => $model->stock,
            'on_sale'        => intval($model->product->on_sale),
            'limit_buy'      => $model->limit_buy,
            'limit_num'      => $model->limit_num,
            'product_id'     => $model->product_id,
            'amount'         => $model->pivot->amount,
        ];
    }

    public function includeProduct($model)
    {
        return $this->item($model->product, new ProductTransformer());
    }
}