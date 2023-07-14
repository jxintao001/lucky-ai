<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * App\Models\Act
 *
 * @property int $id
 * @property string $name
 * @property string|null $cover
 * @property int $order
 * @property string|null $description
 * @property int|null $is_blocked
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property int $shop_id
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\Product[] $products
 * @property-read int|null $products_count
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Act newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Act newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Act query()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Act whereCover($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Act whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Act whereDescription($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Act whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Act whereInitial($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Act whereIsBlocked($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Act whereLogo($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Act whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Act whereOrder($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Act wherePinyinAbbr($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Act whereShopId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Act whereUpdatedAt($value)
 * @mixin \Eloquent
 * @property string $title
 * @property string|null $cover_banner
 * @property \Illuminate\Support\Carbon|null $begin_at
 * @property \Illuminate\Support\Carbon|null $end_at
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Act whereBeginAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Act whereCoverBanner($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Act whereEndAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Act whereTitle($value)
 */
class Act extends Model
{
    // end_at 会自动转为 Carbon 类型
    protected $dates = ['begin_at','end_at'];
    // 不需要 created_at 和 updated_at 字段
    public $timestamps = false;
    
    public function products()
    {
        return $this->hasMany(Product::class);
    }


}
