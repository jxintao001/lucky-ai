<?php

namespace App\Transformers;


class CardTransformer extends BaseTransformer
{
    public function transformData($model)
    {
        return [
            //'id'                 => $model->id,
            'uuid'               => $model->uuid,
            'user_id'            => $model->user_id,
            'secondary_card_no' => $model->secondary_card_no,
            'master_card_no'     => $model->master_card_no,
            'master_user_id'     => $model->master_user_id,
//            'master_user_name'   => $model->master_user_id,
//            'master_food_stamp'  => $model->master_user_id,
            "bound_at"           => !empty($model->bound_at) ? $model->bound_at->toDateTimeString() : '',
            'is_default'         => $model->is_default,
            'is_close'           => $model->is_close,
        ];
    }
}