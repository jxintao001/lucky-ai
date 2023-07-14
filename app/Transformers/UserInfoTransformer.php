<?php

namespace App\Transformers;

class UserInfoTransformer extends BaseTransformer
{
    public function transformData($model)
    {
        return [
            'id'            => $model->id,
            'name'          => $model->name,
            'friend_status' => $model->friend_status ?? 0,
        ];
    }
}