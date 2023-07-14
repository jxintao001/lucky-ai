<?php

namespace App\Transformers;

class CouponCodeTransformer extends BaseTransformer
{
    public function transformData($model)
    {
        return [
            'id' => $model->id,
            'name' => $model->name,
            'cover' => !empty($model->cover) ? config('api.img_host').$model->cover : '',
            'tips_image' => !empty($model->tips_image) ? config('api.img_host').$model->tips_image : '',
            'bgcolor' => $model->getBgColor($model->value),
            'receive_status' => !empty($model->userCouponCodes->toArray()) ? '1' : '0',
            'value' => $model->value,
            'total' => $model->total,
            'surplus' => max(0,$model->total - $model->received),
            'min_amount' => $model->min_amount,
            'use_type' => $model->use_type,
            'target_id' => $model->target_id,
            'not_before' => $model->not_before->toDateTimeString(),
            //'not_before' => !empty($model->not_before) ? $model->not_before->format("Y-m-d") : '',
            'not_after' => $model->not_after->toDateTimeString(),
            //'not_after' => !empty($model->not_after) ? $model->not_after->format("Y-m-d") : '',
            'introduction' => $model->introduction,
        ];
    }

}