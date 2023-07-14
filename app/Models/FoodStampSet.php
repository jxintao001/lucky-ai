<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class FoodStampSet extends Model
{
    protected $table = 'food_stamp_sets';

    // 指明这两个字段是日期类型
    protected $dates = ['begin_at', 'end_at'];

}
