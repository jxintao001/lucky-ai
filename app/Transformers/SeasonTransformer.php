<?php

namespace App\Transformers;


class SeasonTransformer extends BaseTransformer
{
    public function transformData($model)
    {
        return [
            'id'          => $model->id,
            'name'        => $model->name,
            'cover'       => !empty($model->cover) ? config('api.img_host') . $model->cover : '',
            'description' => $model->description,
            'created_at'  => $model->created_at ? $model->created_at->toDateTimeString() : '',
            'updated_at'  => $model->updated_at ? $model->updated_at->toDateTimeString() : '',
        ];
    }
}