<?php

namespace App\Transformers;


class GiftPackageItemTransformer extends BaseTransformer
{
    public function transformData($model)
    {
        return [
            'id'              => $model->id,
            'user_id'         => $model->user_id,
            'product_id'      => $model->product_id,
            'product_sku_id'  => $model->product_sku_id,
            'product_title'   => !empty($model->product_title) ? $model->product_title : $model->product->title,
            'sku_title'       => $model->sku_title,
            'sku_image'       => !empty($model->sku_image) ? config('api.img_host') . $model->sku_image : '',
            'sku_price'       => foodStampValue($model->sku_price),
            'gift_package_id' => $model->gift_package_id,
            'gift_count'      => $model->gift_count,
            'receive_count'   => $model->receive_count,
            'price'           => foodStampValue($model->price),
            'created_at'      => $model->created_at ? $model->created_at->toDateTimeString() : '',
            'updated_at'      => $model->updated_at ? $model->updated_at->toDateTimeString() : '',
        ];
    }
}