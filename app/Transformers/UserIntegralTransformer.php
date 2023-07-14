<?php

namespace App\Transformers;

use App\Models\UserIntegral;

class UserIntegralTransformer extends BaseTransformer
{
    public function transformData($model)
    {
        return [
            'id'          => $model->id,
            'type'        => $model->type,
            'action_type' => $model->type == UserIntegral::TYPE_GET ? $model->get_method : $model->use_method,
            'integral'    => $model->integral,
            'description' => $model->description,
            'created_at'  => !empty($model->created_at) ? $model->created_at->toDateTimeString() : '',
            'updated_at'  => !empty($model->updated_at) ? $model->updated_at->toDateTimeString() : '',
        ];
    }
}