<?php

namespace App\Transformers;

class NewsTransformer extends BaseTransformer
{
    protected $availableIncludes = ['category', 'detail'];

    public function transformData($model)
    {
        return [
            'id' => $model->id,
            'title' => $model->title,
            'description' => $model->description,
            'cover' => !empty($model->cover) ? config('api.img_host').$model->cover : '',
            'language' => $model->language,
            'created_at' => !empty($model->created_at) ? $model->created_at->toDateTimeString() : "",
            'updated_at' => !empty($model->updated_at) ? $model->updated_at->toDateTimeString() : "",
        ];
    }

    public function includeCategory($model)
    {
        return $this->item($model->category, new CategoryTransformer());
    }

    public function includeDetail($model)
    {
//        return $this->item($model->detail, new NewsDetailTransformer());
    }

}