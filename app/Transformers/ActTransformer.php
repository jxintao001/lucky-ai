<?php

namespace App\Transformers;


class ActTransformer extends BaseTransformer
{
    public function transformData($model)
    {
        return [
            'id' => $model->id,
            'name' => $model->name,
            'initial' => $model->initial,
            'title' => $model->title,
            'begin_at' => !empty($model->begin_at) ? $model->begin_at->toDateTimeString() : '',
            'end_at' => !empty($model->end_at) ? $model->end_at->toDateTimeString() : '',
            'end_day' => !empty($model->end_at) ? $model->end_at->format("Y年m月d日") : '',
            'cover' => !empty($model->cover) ? config('api.img_host').$model->cover : '',
            'cover_banner' => !empty($model->cover_banner) ? config('api.img_host').$model->cover_banner : '',
            'description' => $model->description,
        ];
    }
}