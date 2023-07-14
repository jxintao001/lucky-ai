<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * App\Models\Season
 *
 * @property int $id
 * @property string $name
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
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Season newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Season newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Season query()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Season whereCover($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Season whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Season whereDescription($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Season whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Season whereIsBlocked($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Season whereLogo($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Season whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Season whereOrder($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Season whereShopId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Season whereUpdatedAt($value)
 * @mixin \Eloquent
 */
class Season extends Model
{

    public function products()
    {
        return $this->morphedByMany(Product::class, 'seasonable');
    }

}