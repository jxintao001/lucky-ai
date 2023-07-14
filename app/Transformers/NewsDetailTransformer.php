<?php

namespace App\Transformers;

class NewsDetailTransformer extends BaseTransformer
{
    public function transformData($model)
    {
        return [
            'id' => $model->id,
            'content' => $model->content,
        ];
    }
}