<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * App\Models\Video
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
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Video newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Video newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Video query()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Video whereCover($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Video whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Video whereDescription($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Video whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Video whereInitial($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Video whereIsBlocked($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Video whereLogo($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Video whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Video whereOrder($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Video wherePinyinAbbr($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Video whereShopId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Video whereUpdatedAt($value)
 * @mixin \Eloquent
 * @property string $title
 * @property string|null $url
 * @property string|null $cos_url
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Video whereCosUrl($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Video whereTitle($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Video whereUrl($value)
 */
class Video extends Model
{
    
    public function products()
    {
        return $this->hasMany(Product::class);
    }


}
