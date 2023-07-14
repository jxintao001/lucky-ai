<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * App\Models\Notice
 *
 * @property int $id
 * @property string $name
 * @property int $order
 * @property string|null $description
 * @property int|null $is_blocked
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property int $shop_id
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\Product[] $products
 * @property-read int|null $products_count
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Notice newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Notice newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Notice query()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Notice whereCover($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Notice whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Notice whereDescription($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Notice whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Notice whereInitial($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Notice whereIsBlocked($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Notice whereLogo($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Notice whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Notice whereOrder($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Notice wherePinyinAbbr($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Notice whereShopId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Notice whereUpdatedAt($value)
 * @mixin \Eloquent
 * @property string|null $cate
 * @property string|null $title
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Notice whereCate($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Notice whereTitle($value)
 */
class Notice extends Model
{

    public function products()
    {
        return $this->hasMany(Product::class);
    }


}
