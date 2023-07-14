<?php

namespace App\Transformers;

class PostTransformer extends BaseTransformer
{

    public function transformData($model)
    {
        return [
            'id'                 => $model->id,
            'title'              => $model->title,
            'vote_count'         => $model->vote_count,
            'comment_count'      => !empty($model->reviewComments) ? $model->reviewComments->count() : 0,
            'vote_status'        => $model->vote_status,
            'user_follow_status' => $model->user_follow_status,
            'user_id'            => $model->user_id,
            'user_name'          => !empty($model->user->name) ? $model->user->name : '',
            'user_avatar'        => !empty($model->user->avatar) ? $model->user->avatar : '',
            'product_skus'       => $model->product_skus,
            'rating'             => $model->rating,
            'is_open'            => $model->is_open,
            'created_at'         => !empty($model->created_at) ? $model->created_at->diffForHumans() : '',
            'updated_at'         => !empty($model->updated_at) ? $model->updated_at->diffForHumans() : '',
        ];
    }


}