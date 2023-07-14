<?php

namespace App\Transformers;


class MasterCardTransformer extends BaseTransformer
{
    protected $defaultIncludes = ['friend'];

    public function transformData($model)
    {
        return [
            'uuid'        => $model->uuid,
            'card_no'     => $model->master_card_no,
            'user_id'     => $model->master_user_id,
            'user_name'   => !empty($model->masterUser) ? $model->masterUser->name : '',
            'user_avatar' => !empty($model->masterUser) ? $model->masterUser->avatar : '',
            'food_stamp'  => !empty($model->masterUser) ? foodStampValue($model->masterUser->food_stamp) : 0,
            "bound_at"    => !empty($model->bound_at) ? $model->bound_at->toDateTimeString() : '',
            'is_default'  => $model->is_default,
            'is_close'    => $model->is_close,
        ];
    }

    public function includeFriend($model)
    {
        return $this->item($model->masterFriend, new UserFriendsTransformer());
    }

}