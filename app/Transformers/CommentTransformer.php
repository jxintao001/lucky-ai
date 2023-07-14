<?php

namespace App\Transformers;

class CommentTransformer extends BaseTransformer
{

    public function transformData($model)
    {
        return [
            'id'          => $model->id,
            'content'     => $model->content,
            'user_id'     => $model->user_id,
            'user_name'   => !empty($model->user->name) ? $model->user->name : '',
            'user_avatar' => !empty($model->user->avatar) ? $model->user->avatar : '',
            'created_at'  => !empty($model->created_at) ? $model->created_at->diffForHumans() : '',
            'updated_at'  => !empty($model->updated_at) ? $model->updated_at->diffForHumans() : '',
        ];
    }


}