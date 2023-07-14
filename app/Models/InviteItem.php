<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * App\Models\InviteItem
 *
 * @property int $id 邀请记录表
 * @property int $user_id 用户id
 * @property int $invite_user_id 被邀请人用户id
 * @property int|null $coupon_id 优惠券id
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property int $shop_id
 * @property-read \App\Models\User $user
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\InviteItem newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\InviteItem newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\InviteItem query()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\InviteItem whereCouponId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\InviteItem whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\InviteItem whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\InviteItem whereInviteUserId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\InviteItem whereShopId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\InviteItem whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\InviteItem whereUserId($value)
 * @mixin \Eloquent
 */
class InviteItem extends Model
{
    protected $fillable = ['user_id','invite_user_id','coupon_id','shop_id'];
    public $timestamps = true;

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

}
