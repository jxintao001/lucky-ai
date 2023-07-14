<?php

namespace App\Transformers;

class ProductPropertyTransformer extends BaseTransformer
{
    public function transformData($model)
    {
        return [
            'id' => $model->id,
            'name' => $model->name,
            'value' => $model->value,
        ];
    }
}