<?php

namespace App\Transformers;

use App\Models\ProductSku;

class BargainTransformer extends BaseTransformer
{
    protected $availableIncludes = ['items','user','product','productSku','bargainProduct','order'];

    public function transformData($model)
    {
        return [
            "id" => (string)$model->id,
            "user_id" => $model->user_id,
            "order_id" => $model->order_id,
            "product_id" => $model->product_id,
            "sku_id" => $model->sku_id,
            "target_price" => $model->target_price,
            "current_price" => $model->current_price,
            "price" => $model->price,
            "user_count" => $model->user_count,
            "status" => $model->status,
            "closed" => $model->closed,
            "paid_at" => !empty($model->paid_at) ? $model->paid_at->toDateTimeString() : '',
            "finished_at" => !empty($model->finished_at) ? $model->finished_at->toDateTimeString() : '',
            "close_count_down" => $model->closeCountDown($model->created_at),
            "created_at" => !empty($model->created_at) ? $model->created_at->toDateTimeString() : '',
            "updated_at" => !empty($model->updated_at) ? $model->updated_at->toDateTimeString() : '',

        ];
    }

    public function includeItems($model)
    {
        return $this->collection($model->items, new BargainItemTransformer());
    }

    public function includeUser($model)
    {
        return $this->item($model->user, new UserTransformer());
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
    public function includeOrder($model)
    {
        return $this->item($model->order, new OrderTransformer());
    }
}
