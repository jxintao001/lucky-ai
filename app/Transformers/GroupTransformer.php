<?php

namespace App\Transformers;

use App\Models\ProductSku;

class GroupTransformer extends BaseTransformer
{
    protected $availableIncludes = ['items','user'];

    public function transformData($model)
    {
        return [
            "id" => $model->id,
            "user_id" => $model->user_id,
            "order_id" => $model->order_id,
            "product_id" => $model->product_id,
            "sku_id" => $model->sku_id,
            "target_count" => $model->target_count,
            "user_count" => $model->user_count,
            "status" => $model->status,
            "closed" => $model->closed,
            "paid_at" => !empty($model->paid_at) ? $model->paid_at->toDateTimeString() : '',
            "finished_at" => !empty($model->finished_at) ? $model->finished_at->toDateTimeString() : '',
            "close_count_down" => $model->closeCountDown($model->paid_at),
            "created_at" => $model->created_at->toDateTimeString(),
            "updated_at" => $model->updated_at->toDateTimeString(),

        ];
    }

    public function includeItems($model)
    {
        return $this->collection($model->items, new GroupItemTransformer());
    }

    public function includeUser($model)
    {
        return $this->item($model->user, new UserTransformer());
    }

}
