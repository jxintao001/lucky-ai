<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * App\Models\UserIdentity
 *
 * @property int $id
 * @property int $user_id 用户ID
 * @property string $real_name 真实姓名
 * @property string $phone 手机号
 * @property string $idcard_no 身份证号
 * @property string|null $idcard_front 身份证-正面
 * @property string|null $idcard_back 身份证-背面
 * @property int|null $is_default 是否默认
 * @property \Illuminate\Support\Carbon|null $last_used_at
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property int $shop_id
 * @property-read \App\Models\User $user
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\UserIdentity newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\UserIdentity newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\UserIdentity query()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\UserIdentity whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\UserIdentity whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\UserIdentity whereIdcardBack($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\UserIdentity whereIdcardFront($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\UserIdentity whereIdcardNo($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\UserIdentity whereIsDefault($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\UserIdentity whereLastUsedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\UserIdentity wherePhone($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\UserIdentity whereRealName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\UserIdentity whereShopId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\UserIdentity whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\UserIdentity whereUserId($value)
 * @mixin \Eloquent
 * @property int|null $import_flag 是否导入订单身份证
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\UserIdentity whereImportFlag($value)
 */
class UserIdentity extends Model
{
    protected $fillable = [
        'real_name',
        'phone',
        'idcard_no',
        'idcard_front',
        'idcard_back',
        'is_default',
        'last_used_at',
        'shop_id',
    ];
    protected $dates = ['last_used_at'];

    protected static function boot()
    {
        parent::boot();
        static::creating(function ($model) {
            // 创建时自动填充店铺id
            if (auth('api')->user()->shop_id){
                $model->shop_id = auth('api')->user()->shop_id;
            } else {
                return false;
            }
        });
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }


}
