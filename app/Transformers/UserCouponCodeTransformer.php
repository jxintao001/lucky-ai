<?php

namespace App\Transformers;

class UserCouponCodeTransformer extends BaseTransformer
{
    public function transformData($model)
    {
        return [
            'id' => $model->pivot->id,
            'name' => $model->name,
            'cover' => !empty($model->cover) ? config('api.img_host').$model->cover : '',
            'tips_image' => !empty($model->tips_image) ? config('api.img_host').$model->tips_image : '',
            'bgcolor' => $model->getBgColor($model->value),
            'value' => $model->value,
            'total' => $model->total,
            'min_amount' => $model->min_amount,
            //'not_before' => $model->not_before->toDateTimeString(),
            'not_before' => !empty($model->not_before) ? $model->not_before->format("Y-m-d") : '',
            //'not_after' => $model->not_after->toDateTimeString(),
            'not_after' => !empty($model->not_after) ? $model->not_after->format("Y-m-d") : '',
            'use_type' => $model->use_type,
            'target_id' => $model->target_id,
            'used' => $model->pivot->used,
            'used_at' => !empty($model->pivot->used_at) ? $model->pivot->used_at : '',
            'introduction' => $model->introduction,
        ];
    }

}