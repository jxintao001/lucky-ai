<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;


/**
 * App\Models\GiftPackageReceive
 *
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\GiftPackageReceiveItem[] $items
 * @property-read int|null $items_count
 * @property-read \App\Models\GiftPackage $package
 * @property-read \App\Models\User $user
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\GiftPackageReceive newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\GiftPackageReceive newQuery()
 * @method static \Illuminate\Database\Query\Builder|\App\Models\GiftPackageReceive onlyTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\GiftPackageReceive query()
 * @method static \Illuminate\Database\Query\Builder|\App\Models\GiftPackageReceive withTrashed()
 * @method static \Illuminate\Database\Query\Builder|\App\Models\GiftPackageReceive withoutTrashed()
 * @mixin \Eloquent
 * @property int $id
 * @property int $user_id 用户ID
 * @property int|null $gift_package_id 礼品包ID
 * @property int|null $receive_count 领取礼品总数
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property string|null $deleted_at
 * @property int $shop_id
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\GiftPackageReceive whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\GiftPackageReceive whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\GiftPackageReceive whereGiftPackageId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\GiftPackageReceive whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\GiftPackageReceive whereReceiveCount($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\GiftPackageReceive whereShopId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\GiftPackageReceive whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\GiftPackageReceive whereUserId($value)
 */
class GiftPackageReceive extends Model
{
    use SoftDeletes;

    protected $guarded = ['id'];

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

    public function package()
    {
        return $this->belongsTo(GiftPackage::class, 'gift_package_id');
    }

    public function gifts()
    {
        return $this->hasMany(GiftPackageReceiveItem::class, 'receive_id');
    }

    public function items()
    {
        return $this->hasMany(GiftPackageReceiveItem::class, 'receive_id');
    }

}
