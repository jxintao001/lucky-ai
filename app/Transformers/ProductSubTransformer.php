<?php

namespace App\Transformers;

class ProductSubTransformer extends BaseTransformer
{

    public function transformData($model)
    {
        return [
            'id'             => $model->id,
            'product_id'     => $model->product_id,
            'original_price' => foodStampValue($model->original_price),
            'price'          => foodStampValue($model->price),
            'on_sale'        => $model->on_sale,
        ];
    }

}