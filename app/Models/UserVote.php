<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;


class UserVote extends Model
{

    protected $guarded = ['id'];

    protected $dates = [

    ];

    protected static function boot()
    {
        parent::boot();
        static::creating(function ($model) {
            // 创建时自动填充店铺id
            if (!$model->shop_id) {
                if (auth('api')->user()->shop_id) {
                    $model->shop_id = auth('api')->user()->shop_id;
                } else {
                    return false;
                }
            }
        });
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function post()
    {
        return $this->belongsTo(Post::class,'item_id', 'id');
    }


}
