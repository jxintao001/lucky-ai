<?php

namespace App\Transformers;

class ReportTransformer extends BaseTransformer
{
    public function transformData($model)
    {
        return [
            'id'           => $model->id,
            'name'         => $model->name,
            'title'        => $model->title,
            'cover'        => !empty($model->cover) ? config('api.img_host') . $model->cover : '',
            'cover_banner' => !empty($model->cover_banner) ? config('api.img_host') . $model->cover_banner : '',
            'description'  => $model->description,
        ];
    }
}