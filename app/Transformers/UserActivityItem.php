<?php

namespace App\Transformers;

class UserActivityItem extends BaseTransformer
{
    public function transformData($model)
    {
        return [
            "id" => $model->id,
            "user_id" => $model->user->id,
            "name" => $model->user->name,
            "avatar" => $model->user->avatar,
            "click_count" => $model->click_count,
            "score" => $model->score,

        ];
    }


}
