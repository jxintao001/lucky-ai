<?php

namespace App\Transformers;


class WarehouseTransformer extends BaseTransformer
{
    public function transformData($model)
    {
        return [
            'id' => $model->id,
            'name' => $model->name,
            'code' => $model->code,
            'logo' => !empty($model->logo) ? config('api.img_host').$model->logo : '',
            'introduction' => $model->introduction,
        ];
    }
}