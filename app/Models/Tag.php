<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * App\Models\Tag
 *
 * @property int $id
 * @property string $name
 * @property int $hot
 * @property int $new
 * @property int $recommend
 * @property int $order
 * @property string $options
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property int $shop_id
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\Product[] $products
 * @property-read int|null $products_count
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Tag newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Tag newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Tag query()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Tag whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Tag whereHot($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Tag whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Tag whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Tag whereNew($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Tag whereOptions($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Tag whereOrder($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Tag whereRecommend($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Tag whereShopId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Tag whereUpdatedAt($value)
 * @mixin \Eloquent
 */
class Tag extends Model
{
    protected $table = 'tags';

    public function getOptionsAttribute($options)
    {
        if (is_string($options)) {
            $options = explode(',', $options);
        }

        return $options;
    }

    public function setOptionsAttribute($options)
    {
        if (is_array($options)) {
            $options = join(',', $options);
        }

        $this->options = $options;
    }

    public function products(){
        return $this->morphedByMany(Product::class, 'taggable');
    }

}