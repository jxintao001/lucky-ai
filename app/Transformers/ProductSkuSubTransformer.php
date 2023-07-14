<?php

namespace App\Transformers;

class ProductSkuSubTransformer extends BaseTransformer
{

    public function transformData($model)
    {
        return [
            'id'             => $model->id,
            'product_id'     => $model->product_id,
            'product_sku_id' => $model->product_sku_id,
            'tax_price'      => foodStampValue($model->tax_price),
        ];
    }

}