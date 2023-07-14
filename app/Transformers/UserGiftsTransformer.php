<?php

namespace App\Transformers;

class UserGiftsTransformer extends BaseTransformer
{
    public function transformData($model)
    {
        return [
            'id'             => $model->id,
            'user_id'        => $model->user_id,
            'product_id'     => $model->product_id,
            'product_sku_id' => $model->product_sku_id,
            'product_title'  => !empty($model->product_title) ? $model->product_title : $model->product->title,
            'sku_title'      => $model->sku_title,
            'sku_image'      => !empty($model->sku_image) ? config('api.img_host') . $model->sku_image : '',
            'sku_price'      => foodStampValue($model->sku_price),
            'sku_type'       => $model->productSku->type,
            'sku_is_virtual' => $model->productSku->is_virtual,
            'sku_integral'   => $model->productSku->integral,
            'sku_food_stamp' => foodStampValue($model->productSku->integral),
            'count'          => $model->count,
            'created_at'     => !empty($model->created_at) ? $model->created_at->toDateTimeString() : '',
            'updated_at'     => !empty($model->updated_at) ? $model->updated_at->toDateTimeString() : '',
        ];
    }
}