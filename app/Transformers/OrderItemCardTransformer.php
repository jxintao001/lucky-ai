<?php

namespace App\Transformers;

class OrderItemCardTransformer extends BaseTransformer
{

    public function transformData($model)
    {
        return [
            'id'          => $model->id,
            'card_title'  => $model->sku->title,
            'card_no'     => $model->card_no,
            'card_key'    => $model->card_key,
            'exchange'    => (bool)$model->exchange,
            'exchange_at' => !empty($model->exchange_at) ? $model->exchange_at->toDateTimeString() : '',
            'used'        => (bool)$model->used,
            'used_at'     => !empty($model->used_at) ? $model->used_at->toDateTimeString() : '',
        ];
    }

}