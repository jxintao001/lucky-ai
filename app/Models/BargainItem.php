<?php
namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;

/**
 * App\Models\BargainItem
 *
 * @property int $id
 * @property int|null $bargain_id
 * @property int $user_id
 * @property float $current_price
 * @property float $cut_price
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property int $shop_id
 * @property-read \App\Models\Bargain|null $bargain
 * @property-read \App\Models\User $user
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\BargainItem newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\BargainItem newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\BargainItem query()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\BargainItem whereBargainId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\BargainItem whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\BargainItem whereCurrentPrice($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\BargainItem whereCutPrice($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\BargainItem whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\BargainItem whereShopId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\BargainItem whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\BargainItem whereUserId($value)
 * @mixin \Eloquent
 */
class BargainItem extends Model
{
    protected $fillable = [
        'bargain_id',
        'user_id',
        'current_price',
        'cut_price',
        'created_at',
        'updated_at',
        'shop_id',
    ];

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


    public function bargain()
    {
        return $this->belongsTo(Bargain::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
//    // 创建一个访问器，返回当前还款计划需还款的总金额
//    public function getTotalAttribute()
//    {
//        $total = big_number($this->base)->add($this->fee);
//        if (!is_null($this->fine)) {
//            $total->add($this->fine);
//        }
//
//        return $total->getValue();
//    }
//
//    // 创建一个访问器，返回当前还款计划是否已经逾期
//    public function getIsOverdueAttribute()
//    {
//        return Carbon::now()->gt($this->due_date);
//    }
}
