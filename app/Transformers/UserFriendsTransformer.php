<?php

namespace App\Transformers;

class UserFriendsTransformer extends BaseTransformer
{
    public function transformData($model)
    {
        $friend_name = '';
        if (!is_null($model->friend_name) && $model->friend_name !== '') {
            $friend_name = $model->friend_name;
        } elseif (!empty($model->friend->name)) {
            $friend_name = $model->friend->name;
        }
        return [
            'id'              => $model->id ?? 0,
            'user_id'         => $model->user_id ?? 0,
            'friend_id'       => $model->friend_id ?? 0,
            'friend_name'     => $friend_name,
            'friend_avatar'   => isset($model->friend_id) && !empty($model->friend->avatar) ? $model->friend->avatar : '',
            'friend_phone'    => $model->friend_phone ?? '',
            'friend_birthday' => $model->friend_birthday ?? '',
            'relationship'    => $model->relationship ?? '',
            'remark'          => $model->remark ?? '',
            'created_at'      => !empty($model->created_at) ? $model->created_at->toDateTimeString() : '',
            'updated_at'      => !empty($model->updated_at) ? $model->updated_at->toDateTimeString() : '',
        ];
    }
}