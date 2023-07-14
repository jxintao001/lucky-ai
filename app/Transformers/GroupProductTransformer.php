<?php

namespace App\Transformers;

class GroupProductTransformer extends BaseTransformer
{
    public function transformData($model)
    {
        return [
            'id' => $model->id,
            'product_id' => $model->product_id,
            'price' => $model->price,
            'target_count' => $model->target_count,
            'begin_at' => !empty($model->begin_at) ? $model->begin_at->toDateTimeString() : '',
            'end_at' => !empty($model->end_at) ? $model->end_at->toDateTimeString() : '',
        ];
    }

}