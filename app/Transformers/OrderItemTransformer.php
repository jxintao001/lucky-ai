<?php

namespace App\Transformers;

class OrderItemTransformer extends BaseTransformer
{
    protected $defaultIncludes = ['product', 'productSku'];

    public function transformData($model)
    {
        return [
            'id'             => $model->id,
            'order_id'       => $model->order_id,
            'product_id'     => $model->product_id,
            'product_sku_id' => $model->product_sku_id,
            'price'          => foodStampValue($model->price),
            'amount'         => $model->amount,
        ];
    }

    public function includeProduct($model)
    {
        return $this->item($model->product, new ProductTransformer());
    }

    public function includeProductSku($model)
    {
        return $this->item($model->productSku, new ProductSkuTransformer());
    }

    public function includeBargainProduct($model)
    {
        return $this->item($model->bargainProduct, new BargainProductTransformer());
    }

    public function includeGroupProduct($model)
    {
        return $this->item($model->groupProduct, new GroupProductTransformer());
    }

}