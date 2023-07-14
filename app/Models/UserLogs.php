<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * App\Models\UserLogs
 *
 * @property int $id
 * @property string|null $action 操作
 * @property int|null $user_id 用户id
 * @property int|null $shop_id 店铺id
 * @property \Illuminate\Support\Carbon|null $created_at 创建时间
 * @property \Illuminate\Support\Carbon|null $updated_at 更新时间
 * @property-read \App\Models\User|null $user
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\UserLogs newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\UserLogs newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\UserLogs query()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\UserLogs whereAction($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\UserLogs whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\UserLogs whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\UserLogs whereShopId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\UserLogs whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\UserLogs whereUserId($value)
 * @mixin \Eloquent
 */
class UserLogs extends Model
{
    protected $fillable = [
        'user_id',
        'shop_id',
        'action',
    ];

//    protected static function boot()
//    {
//        parent::boot();
//        static::creating(function ($model) {
//            // 创建时自动填充店铺id
//            if (auth('api')->user()->shop_id){
//                $model->shop_id = auth('api')->user()->shop_id;
//            } else {
//                return false;
//            }
//        });
//    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

}
