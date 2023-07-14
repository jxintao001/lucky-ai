<?php

namespace App\Transformers;

class UserGiftItemsTransformer extends BaseTransformer
{
    public function transformData($model)
    {
        return [
            'id'              => $model->id,
            'type'            => $model->type ?? 'get',
            'user_id'         => $model->user_id,
            'order_id'        => $model->order_id ?? 0,
            'gift_package_id' => $model->gift_package_id,
            'product_id'      => $model->product_id,
            'product_sku_id'  => $model->product_sku_id,
            'product_title'   => !empty($model->product_title) ? $model->product_title : $model->product->title,
            'sku_title'       => $model->sku_title,
            'sku_image'       => !empty($model->sku_image) ? config('api.img_host') . $model->sku_image : '',
            'sku_price'       => foodStampValue($model->sku_price),
            'amount'          => $model->amount ?? $model->receive_count,
            'price'           => !empty($model->price) ? foodStampValue($model->price) : foodStampValue($model->sku_price),
            'get_method'      => $model->get_method ?? 'gift_package',
            'use_method'      => $model->use_method,
            'created_at'      => !empty($model->created_at) ? $model->created_at->toDateTimeString() : '',
            'updated_at'      => !empty($model->updated_at) ? $model->updated_at->toDateTimeString() : '',
        ];
    }
}