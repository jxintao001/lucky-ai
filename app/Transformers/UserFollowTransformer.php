<?php

namespace App\Transformers;

class UserFollowTransformer extends BaseTransformer
{
    public function transformData($model)
    {
        return [
            'id'     => $model->id,
            'name'   => $model->name,
            'avatar' => !empty($model->avatar) ? $model->avatar : '',
        ];
    }
}