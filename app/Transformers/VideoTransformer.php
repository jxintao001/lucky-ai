<?php

namespace App\Transformers;


class VideoTransformer extends BaseTransformer
{
    public function transformData($model)
    {
        return [
            'id' => $model->id,
            'cover' =>!empty($model->cover) ? config('api.img_host').$model->cover : '',
            'title' => $model->title,
            'url' => !empty($model->url) ? config('api.img_host').$model->url : '',
            'cos_url' => !empty($model->cos_url) ? config('api.img_host').$model->cos_url : '',
            'description' => $model->description,
        ];
    }
}