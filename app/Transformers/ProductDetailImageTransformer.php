<?php

namespace App\Transformers;

class ProductDetailImageTransformer extends BaseTransformer
{
    public function transformData($model)
    {
        return [
            'id' => $model->id,
            'image' => !empty($model->image) ? config('api.img_host').$model->image : '',
        ];
    }
}