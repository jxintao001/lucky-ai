<?php

namespace App\Transformers;

class UserFoodStampTransformer extends BaseTransformer
{
    public function transformData($model)
    {
        return [
            'id'                => $model->id,
            'type'              => $model->type,
            'action_type'       => $model->action_type,
            'order_id'          => $model->order_id,
            'order_no'          => $model->order_no,
            'user_name'         => isset($model->friend->friend_name) && ($model->friend->friend_name !== '') ? $model->friend->friend_name : $model->user->name,
            'food_stamp_before' => foodStampValue($model->food_stamp_before),
            'food_stamp'        => foodStampValue($model->food_stamp),
            'food_stamp_after'  => foodStampValue($model->food_stamp_after),
            'description'       => $model->description,
            'created_at'        => !empty($model->created_at) ? $model->created_at->toDateTimeString() : '',
            'updated_at'        => !empty($model->updated_at) ? $model->updated_at->toDateTimeString() : '',
        ];
    }
}