<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * App\Models\Brand
 *
 * @property int $id
 * @property string $name
 * @property string|null $initial
 * @property string|null $pinyin_abbr
 * @property string|null $logo
 * @property string|null $cover
 * @property int $order
 * @property string|null $description
 * @property int|null $is_blocked
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property int $shop_id
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\Product[] $products
 * @property-read int|null $products_count
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Brand newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Brand newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Brand query()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Brand whereCover($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Brand whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Brand whereDescription($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Brand whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Brand whereInitial($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Brand whereIsBlocked($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Brand whereLogo($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Brand whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Brand whereOrder($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Brand wherePinyinAbbr($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Brand whereShopId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Brand whereUpdatedAt($value)
 * @mixin \Eloquent
 * @property string|null $country
 * @property string|null $cover_banner
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Brand whereCountry($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Brand whereCoverBanner($value)
 */
class Brand extends Model
{

    public function products()
    {
        return $this->hasMany(Product::class);
    }


}
