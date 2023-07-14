<?php

namespace App\Transformers;


class GiftPackageReceiveItemTransformer extends BaseTransformer
{
    //protected $defaultIncludes = ['user'];

    public function transformData($model)
    {
        return [
            'id'              => $model->id,
            'user_id'         => $model->user_id,
            'user_name'       => !empty($model->user->name) ? $model->user->name : '',
            'user_avatar'     => !empty($model->user->avatar) ? $model->user->avatar : '',
            'receive_id'      => $model->receive_id,
            'gift_package_id' => $model->gift_package_id,
            'product_id'      => $model->product_id,
            'product_sku_id'  => $model->product_sku_id,
            'product_title'   => !empty($model->product_title) ? $model->product_title : $model->product->title,
            'sku_title'       => $model->sku_title,
            'sku_image'       => !empty($model->sku_image) ? config('api.img_host') . $model->sku_image : '',
            'sku_price'       => foodStampValue($model->sku_price),
            'receive_count'   => $model->receive_count,
            'created_at'      => $model->created_at ? $model->created_at->toDateTimeString() : '',
            'updated_at'      => $model->updated_at ? $model->updated_at->toDateTimeString() : '',
        ];
    }

    public function includeUser($model)
    {
        return $this->item($model->user, new UserTransformer());
    }
}