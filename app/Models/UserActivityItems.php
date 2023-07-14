<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * App\Models\UserActivityItems
 *
 * @property int $id
 * @property string $type
 * @property int $user_id
 * @property int $click_count
 * @property int $score
 * @property string $prize_type
 * @property int $prize_id
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property int $shop_id
 * @property-read \App\Models\CouponCode $couponCode
 * @property-read \App\Models\User $user
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\UserActivityItems newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\UserActivityItems newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\UserActivityItems query()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\UserActivityItems whereClickCount($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\UserActivityItems whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\UserActivityItems whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\UserActivityItems wherePrizeId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\UserActivityItems wherePrizeType($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\UserActivityItems whereScore($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\UserActivityItems whereShopId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\UserActivityItems whereType($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\UserActivityItems whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\UserActivityItems whereUserId($value)
 * @mixin \Eloquent
 */
class UserActivityItems extends Model
{
    protected $fillable = ['type','user_id','click_count','score','prize_type','prize_id','shop_id'];

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

    public function couponCode()
    {
        return $this->belongsTo(CouponCode::class, 'prize_id', 'id');
    }

}
