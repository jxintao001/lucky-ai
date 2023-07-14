<?php

namespace App\Transformers;

class OrderExchangeItemTransformer extends BaseTransformer
{

    public function transformData($model)
    {
        return [
            'id'                => $model->id,
            'order_id'          => $model->order_id,
            'product_id'        => $model->product_id,
            'product_sku_id'    => $model->product_sku_id,
            'product_title'     => !empty($model->product_title) ? $model->product_title : $model->product->title,
            'sku_title'         => !empty($model->sku_title) ? $model->sku_title : $model->productSku->title,
            'sku_image'         => !empty($model->sku_image) ? config('api.img_host') . $model->sku_image : config('api.img_host') . $model->product->cover,
            'sku_price'         => !empty($model->sku_price) ? foodStampValue($model->sku_price) : foodStampValue($model->productSku->price),
            'integral_amount'   => !empty($model->sku_integral) ? $model->sku_integral * $model->amount : $model->productSku->integral * $model->amount,
            'food_stamp_amount' => !empty($model->sku_stamp_amount) ? foodStampValue($model->sku_stamp_amount) * $model->amount : $model->productSku->integral * $model->amount,
            'price'             => foodStampValue($model->price),
            'amount'            => $model->amount,
        ];
    }

}