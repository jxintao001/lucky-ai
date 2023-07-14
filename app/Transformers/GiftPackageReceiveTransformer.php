<?php

namespace App\Transformers;


class GiftPackageReceiveTransformer extends BaseTransformer
{
    protected $availableIncludes = ['gifts'];

    //protected $defaultIncludes = ['user'];

    public function transformData($model)
    {
        return [
            'id'                => $model->id,
            'user_id'           => $model->user_id,
            'user_name'         => !empty($model->user->name) ? $model->user->name : '',
            'user_avatar'       => !empty($model->user->avatar) ? $model->user->avatar : '',
            'give_user_id'      => $model->package->user->id,
            'give_user_name'    => $model->package->user->name,
            'give_user_avatar'  => !empty($model->package->user->avatar) ? $model->package->user->avatar : '',
            'gift_package_id'   => $model->gift_package_id,
            'gift_package_no'   => $model->package->no,
            'gift_package_code' => $model->package->code,
            'receive_count'     => $model->receive_count,
            'created_at'        => $model->created_at ? $model->created_at->toDateTimeString() : '',
            'updated_at'        => $model->updated_at ? $model->updated_at->toDateTimeString() : '',
        ];
    }

    public function includeGifts($model)
    {
        return $this->collection($model->gifts, new UserGiftItemsTransformer());
    }

    public function includeUser($model)
    {
        return $this->item($model->user, new UserTransformer());
    }
}