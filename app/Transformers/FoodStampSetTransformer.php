<?php

namespace App\Transformers;

class FoodStampSetTransformer extends BaseTransformer
{

    public function transformData($model)
    {
        return [
            'id'                => $model['id'],
            'title'             => $model['title'],
            'food_stamp_amount' => foodStampValue($model['food_stamp_amount']),
            'give_amount'       => foodStampValue($model['give_amount']),
            'description'       => $model['description'],
        ];
    }

}
