<?php

namespace App\Transformers;

class GiftPackageTransformer extends BaseTransformer
{
    protected $availableIncludes = ['items', 'receives', 'receive_items', 'template', 'gifts'];

    public function transformData($model)
    {
        return [
            'no'                 => $model->no,
            'code'               => $model->code,
            'type'               => $model->type,
            'status'             => $model->status,
            'title'              => $model->title,
            'user_id'            => $model->user_id,
            'user_name'          => !empty($model->user->name) ? $model->user->name : '',
            'user_avatar'        => !empty($model->user->avatar) ? $model->user->avatar : '',
            'gift_count'         => $model->gift_count,
            'gift_receive_count' => $model->gift_receive_count,
            'set_count'          => $model->set_count,
            'set_receive_count'  => $model->set_receive_count,
            'receive_limit'      => $model->receive_limit,
            'receive_status'     => $model->receive_status ? 1 : 0,
            'wish_text'          => $model->wish_text,
            'wish_image'         => $model->wish_image,
            'wish_image2'        => $model->wish_image2,
            'wish_audio'         => $model->wish_audio,
            'wish_video'         => $model->wish_video,
            'template_id'        => $model->template_id,
            'wechat_group_id'    => $model->wechat_group_id,
            'question'           => $model->question,
            'answer'             => $model->answer,
            'closed'             => $model->closed,
            'start_at'           => !empty($model->start_at) ? $model->start_at->toDateTimeString() : '',
            'finished_at'        => !empty($model->finished_at) ? $model->finished_at->toDateTimeString() : '',
            'created_at'         => !empty($model->created_at) ? $model->created_at->toDateTimeString() : '',
            'updated_at'         => !empty($model->updated_at) ? $model->updated_at->toDateTimeString() : '',
        ];
    }

    public function includeItems($model)
    {
        return $this->collection($model->items, new GiftPackageItemTransformer());
    }

    public function includeReceives($model)
    {
        return $this->collection($model->receives, new GiftPackageReceiveTransformer());
    }

    public function includeReceiveItems($model)
    {
        return $this->collection($model->receiveItems, new GiftPackageReceiveItemTransformer());
    }

    public function includeTemplate($model)
    {
        return $this->item($model->template, new GiftPackageTemplateTransfomer());
    }

    public function includeGifts($model)
    {
        return $this->collection($model->gifts, new UserGiftItemsTransformer());
    }

}