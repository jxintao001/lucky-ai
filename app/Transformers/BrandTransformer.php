<?php

namespace App\Transformers;


class BrandTransformer extends BaseTransformer
{
    public function transformData($model)
    {
        return [
            'id' => $model->id,
            'name' => $model->name,
            'initial' => $model->initial,
            'pinyin_abbr' => $model->pinyin_abbr,
            'logo' => !empty($model->logo) ? config('api.img_host').$model->logo : '',
            'cover' => !empty($model->cover) ? config('api.img_host').$model->cover : '',
            'cover_banner' => !empty($model->cover_banner) ? config('api.img_host').$model->cover_banner : '',
            'country' => $model->country,
            'description' => $model->description,
        ];
    }
}