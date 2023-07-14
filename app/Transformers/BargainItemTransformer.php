<?php

namespace App\Transformers;

use App\Models\ProductSku;

class BargainItemTransformer extends BaseTransformer
{
    protected $defaultIncludes = ['user'];

    public function transformData($model)
    {
        return [
            "id" => $model->id,
            "bargain_id" => $model->bargain_id,
            "user_id" => $model->user_id,
            "current_price" => $model->current_price,
            "cut_price" => $model->cut_price,
            "created_at" => $model->created_at->toDateTimeString(),
            "updated_at" => $model->updated_at->toDateTimeString(),

        ];
    }

    public function includeUser($model)
    {
        return $this->item($model->user, new UserTransformer());
    }


}
