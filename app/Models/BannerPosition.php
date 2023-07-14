<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * App\Models\BannerPosition
 *
 * @property int $id id
 * @property string $name 位置名称
 * @property string $code 位置code
 * @property string $params 跳转所需参数名；逗号分隔
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property int $shop_id
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\BannerPosition newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\BannerPosition newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\BannerPosition query()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\BannerPosition whereCode($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\BannerPosition whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\BannerPosition whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\BannerPosition whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\BannerPosition whereParams($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\BannerPosition whereShopId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\BannerPosition whereUpdatedAt($value)
 * @mixin \Eloquent
 */
class BannerPosition extends Model
{

    protected $table = 'banner_positions';
}
