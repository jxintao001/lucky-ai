<?php

namespace App\Transformers;

use App\Models\ProductSku;

class GroupItemTransformer extends BaseTransformer
{
    protected $defaultIncludes = ['user'];
    protected $availableIncludes = ['user','group','product','productSku','groupProduct','order'];

    public function transformData($model)
    {
        return [
            "id" => $model->id,
            "user_id" => $model->user_id,
            "order_id" => $model->order_id,
            "product_id" => $model->product_id,
            "sku_id" => $model->sku_id,
            "status" => $model->status,
            "is_head" => $model->is_head,
            "closed" => $model->closed,
            "paid_at" => !empty($model->paid_at) ? $model->paid_at->toDateTimeString() : '',
            "created_at" => $model->created_at->toDateTimeString(),
            "updated_at" => $model->updated_at->toDateTimeString(),

        ];
    }

    public function includeUser($model)
    {
        return $this->item($model->user, new UserTransformer());
    }
    public function includeGroup($model)
    {
        return $this->item($model->group, new GroupTransformer());
    }
    public function includeProduct($model)
    {
        return $this->item($model->product, new ProductTransformer());
    }
    public function includeProductSku($model)
    {
        return $this->item($model->productSku, new ProductSkuTransformer());
    }
    public function includeGroupProduct($model)
    {
        return $this->item($model->groupProduct, new GroupProductTransformer());
    }
    public function includeOrder($model)
    {
        return $this->item($model->order, new OrderTransformer());
    }
}
