<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * App\Models\UserAddress
 *
 * @property int $id
 * @property int $user_id
 * @property string $province
 * @property string $city
 * @property string|null $district
 * @property string $address
 * @property string|null $zip
 * @property string $contact_name
 * @property string $contact_phone
 * @property string|null $real_name 真实姓名
 * @property string|null $idcard_no 身份证号
 * @property \Illuminate\Support\Carbon|null $last_used_at
 * @property int|null $is_default
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property int $shop_id
 * @property-read mixed $full_address
 * @property-read \App\Models\User $user
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\UserAddress newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\UserAddress newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\UserAddress query()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\UserAddress whereAddress($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\UserAddress whereCity($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\UserAddress whereContactName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\UserAddress whereContactPhone($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\UserAddress whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\UserAddress whereDistrict($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\UserAddress whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\UserAddress whereIdcardNo($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\UserAddress whereIsDefault($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\UserAddress whereLastUsedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\UserAddress whereProvince($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\UserAddress whereRealName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\UserAddress whereShopId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\UserAddress whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\UserAddress whereUserId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\UserAddress whereZip($value)
 * @mixin \Eloquent
 * @property string|null $phone
 * @property int|null $import_flag 是否导入订单地址
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\UserAddress whereImportFlag($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\UserAddress wherePhone($value)
 */
class UserAddress extends Model
{
    protected $fillable = [
        'province',
        'city',
        'district',
        'address',
        'zip',
        'contact_name',
        'contact_phone',
        'real_name',
        'idcard_no',
        'phone',
        'is_default',
        'last_used_at',
        'shop_id',
    ];
    protected $dates = ['last_used_at'];
    protected $appends = ['full_address'];

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

    public function getFullAddressAttribute()
    {
        return "{$this->province}{$this->city}{$this->district}{$this->address}";
    }
}
