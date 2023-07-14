<?php

namespace App\Transformers;

class UserTransformer extends BaseTransformer
{
    public function transformData($model)
    {
        return [
            'id'              => $model->id,
            'name'            => $model->name,
            'admin_name'      => $model->admin_name,
            'real_name'       => $model->real_name,
            'phone'           => $model->phone,
            'email'           => $model->email,
            'gender'          => $model->gender,
            'level'           => (string)$model->level,
            'level_title'           => $model->_level->name,
            'inviter_id'      => $model->inviter_id,
            'inviter_code'    => $model->inviter_code,
            'inviter_num'     => $model->inviter_num,
            'deposit'         => $model->deposit,
            'integral'        => $model->integral,
            'master_card_no'  => $model->master_card_no,
            'country'         => $model->country,
            'province'        => $model->province,
            'city'            => $model->city,
            'district'        => $model->district,
            'birthday'        => $model->birthday,
            'food_stamp'      => foodStampValue($model->food_stamp),
            'avatar'          => $model->avatar,
            'introduction'    => $model->introduction,
            'is_banned'       => $model->is_banned,
            'is_modified'     => $model->is_modified,
            'group_count'     => $model->group_count,
            'bargain_count'   => $model->bargain_count,
            'order_count'     => $model->order_count,
            'gift_count'      => $model->gift_count,
            'shop_id'         => $model->shop_id,
            'last_shop_id'    => $model->last_shop_id,
            'last_actived_at' => $model->last_actived_at,
            'created_at'      => $model->created_at->toDateTimeString(),
            'updated_at'      => $model->updated_at->toDateTimeString(),
            'view_shop'       => (string)$model->view_shop,
        ];
    }
}