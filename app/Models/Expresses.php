<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * App\Models\Expresses
 *
 * @property int $id 自增id
 * @property string $name 快递名称
 * @property string|null $code code
 * @property string|null $logo logo
 * @property string|null $introduction 介绍
 * @property int|null $is_banned 是否屏蔽
 * @property \Illuminate\Support\Carbon|null $created_at 创建时间
 * @property \Illuminate\Support\Carbon|null $updated_at 更新时间
 * @property int|null $shop_id 店铺id
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Expresses newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Expresses newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Expresses query()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Expresses whereCode($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Expresses whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Expresses whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Expresses whereIntroduction($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Expresses whereIsBanned($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Expresses whereLogo($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Expresses whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Expresses whereShopId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Expresses whereUpdatedAt($value)
 * @mixin \Eloquent
 */
class Expresses extends Model
{


}
