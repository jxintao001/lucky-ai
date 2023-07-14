<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * App\Models\Category
 *
 * @property int $id
 * @property string $name
 * @property string|null $logo
 * @property string|null $cover
 * @property int|null $parent_id
 * @property int $is_directory
 * @property int $level
 * @property string $path
 * @property int $order
 * @property string|null $description
 * @property int|null $tax_rate_flag
 * @property float|null $step_amount
 * @property int|null $is_blocked
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property int $shop_id
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Category newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Category newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Category query()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Category whereCover($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Category whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Category whereDescription($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Category whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Category whereIsBlocked($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Category whereIsDirectory($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Category whereLevel($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Category whereLogo($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Category whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Category whereOrder($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Category whereParentId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Category wherePath($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Category whereShopId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Category whereStepAmount($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Category whereTaxRateFlag($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Category whereUpdatedAt($value)
 * @mixin \Eloquent
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\Video[] $videos
 * @property-read int|null $videos_count
 */
class Category extends Model
{

    protected $table = 'categories';

    public function videos()
    {
        return $this->hasMany(Video::class, 'category_id', 'id');
    }
    
}
