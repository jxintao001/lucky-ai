<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * App\Models\Level
 *
 * @property int $id id
 * @property string|null $name 名称
 * @property int $level 等级
 * @property string|null $icon logo
 * @property int $discount 折扣
 * @property string|null $description 等级描述
 * @property int $is_blocked 是否屏蔽（1=屏蔽；0=未屏蔽）
 * @property \Illuminate\Support\Carbon|null $created_at 创建时间
 * @property \Illuminate\Support\Carbon|null $updated_at 更新时间
 * @property int $shop_id 店铺id
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Level newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Level newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Level query()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Level whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Level whereDescription($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Level whereDiscount($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Level whereIcon($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Level whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Level whereIsBlocked($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Level whereLevel($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Level whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Level whereShopId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Level whereUpdatedAt($value)
 * @mixin \Eloquent
 */
class Level extends Model
{

    public function getAdjustedPrice($orderAmount)
    {
        return number_format($orderAmount * (100 - $this->discount) / 100, 2, '.', '');
    }

}
