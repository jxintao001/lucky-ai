<?php

namespace App\Transformers;


class GiftPackageTemplateTransfomer extends BaseTransformer
{
    public function transformData($model)
    {
        return [
            'id'            => $model->id,
            'season_id'     => $model->season_id,
            'title'         => $model->title,
            'description'   => $model->description,
            'image'         => $model->image,
            'image_preview' => $model->image_preview,
            'image_share'   => $model->image_share,
            'audio'         => $model->audio,
            'video'         => $model->video,
            'created_at'    => !empty($model->created_at) ? $model->created_at->toDateTimeString() : '',
            'updated_at'    => !empty($model->updated_at) ? $model->updated_at->toDateTimeString() : '',
        ];
    }
}