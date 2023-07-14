<?php

namespace App\Transformers;

class BargainProductTransformer extends BaseTransformer
{
    public function transformData($model)
    {
        return [
            'id' => $model->id,
            'product_id' => $model->product_id,
            'target_price' => $model->target_price,
            'begin_at' => !empty($model->begin_at) ? $model->begin_at->toDateTimeString() : '',
            'end_at' => !empty($model->end_at) ? $model->end_at->toDateTimeString() : '',
            'end_time' => !empty($model->end_at) ? strtotime($model->end_at) : '',
            'end_day' => !empty($model->end_at) ? $model->end_at->format("Y年m月d日") : '',
            'is_open ' => $model->is_open,
        ];
    }

}