<?php

namespace App\Transformers;

class ShopsTransformer extends BaseTransformer
{
    public function transformData($model)
    {
        return [
            'id' => $model->id,
            'name' => $model->name,
            'logo' => !empty($model->logo) ? config('api.img_host').$model->logo : '',
            'introduction' => $model->introduction,
        ];
    }
}