<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * App\Models\UserCouponCode
 *
 * @property int $id
 * @property int $user_id
 * @property int $coupon_code_id
 * @property bool|null $used
 * @property \Illuminate\Support\Carbon|null $used_at
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property int $shop_id
 * @property-read \App\Models\CouponCode $couponCode
 * @property-read \App\Models\User $user
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\UserCouponCode newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\UserCouponCode newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\UserCouponCode query()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\UserCouponCode whereCouponCodeId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\UserCouponCode whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\UserCouponCode whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\UserCouponCode whereShopId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\UserCouponCode whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\UserCouponCode whereUsed($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\UserCouponCode whereUsedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\UserCouponCode whereUserId($value)
 * @mixin \Eloquent
 */
class UserCouponCode extends Model
{
    protected $fillable = ['used','used_at','shop_id'];
    protected $dates = ['used_at'];
    protected $casts = [
        'used'    => 'boolean'
    ];

    protected static function boot()
    {
        parent::boot();
        static::creating(function ($model) {
            // 创建时自动填充店铺id
            if (!$model->shop_id){
                if (auth('api')->user()->shop_id){
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

    public function couponCode()
    {
        return $this->belongsTo(CouponCode::class);
    }

}
