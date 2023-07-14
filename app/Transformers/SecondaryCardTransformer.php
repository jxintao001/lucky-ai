<?php

namespace App\Transformers;


class SecondaryCardTransformer extends BaseTransformer
{
    protected $defaultIncludes = ['friend'];

    public function transformData($model)
    {
        return [
            'uuid'        => $model->uuid,
            'card_no'     => $model->secondary_card_no,
            'user_id'     => $model->user_id,
            'user_name'   => !empty($model->user) ? $model->user->name : '',
            'user_avatar' => !empty($model->user) ? $model->user->avatar : '',
            'food_stamp'  => !empty($model->masterUser) ? foodStampValue($model->masterUser->food_stamp) : 0,
            "bound_at"    => !empty($model->bound_at) ? $model->bound_at->toDateTimeString() : '',
            'is_default'  => $model->is_default,
            'is_close'    => $model->is_close,
        ];
    }

    public function includeFriend($model)
    {
        return $this->item($model->friend, new UserFriendsTransformer());
    }

}