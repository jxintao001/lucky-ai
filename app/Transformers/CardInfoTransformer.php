<?php

namespace App\Transformers;


class CardInfoTransformer extends BaseTransformer
{
    public function transformData($model)
    {
        return [
            'uuid'         => $model->uuid,
            'card_no'      => $model->secondary_card_no,
            "bound_at"     => !empty($model->bound_at) ? $model->bound_at->toDateTimeString() : '',
            "bound_status" => !empty($model->bound_at) || !empty($model->other_bound_status) ? 1 : 0,
        ];
    }
}