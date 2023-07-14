<?php

namespace App\Transformers;


class NoticeTransformer extends BaseTransformer
{
    public function transformData($model)
    {
        return [
            'id' => $model->id,
            'cate' => $model->cate,
            'title' => $model->title,
            'description' => $model->description,
        ];
    }
}