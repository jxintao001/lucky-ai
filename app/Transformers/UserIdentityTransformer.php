<?php

namespace App\Transformers;

class UserIdentityTransformer extends BaseTransformer
{
    public function transformData($model)
    {
        return [
            'id' => $model->id,
            'real_name' => $model->real_name,
            'phone' => $model->phone,
            'idcard_no' => $model->idcard_no,
            'idcard_front' => !empty($model->idcard_front) ? config('api.img_host').$model->idcard_front : '',
            'idcard_back' => !empty($model->idcard_back) ? config('api.img_host').$model->idcard_back : '',
            'is_default' => $model->is_default,
            'last_used_at' => !empty($model->last_used_at) ? $model->last_used_at->toDateTimeString() : '',
            'created_at' => !empty($model->created_at) ? $model->created_at->toDateTimeString() : '',
            'updated_at' => !empty($model->updated_at) ? $model->updated_at->toDateTimeString() : '',
        ];
    }
}