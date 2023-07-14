<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;


/**
 * App\Models\Channel
 *
 * @property int $id
 * @property string $uuid uuid
 * @property string|null $name
 * @property string|null $code
 * @property string|null $logo
 * @property string|null $qr_code 二维码
 * @property string|null $introduction
 * @property int|null $is_banned
 * @property string $secret_key 秘钥
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property int $shop_id 店铺id
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Channel newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Channel newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Channel query()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Channel whereCode($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Channel whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Channel whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Channel whereIntroduction($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Channel whereIsBanned($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Channel whereLogo($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Channel whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Channel whereQrCode($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Channel whereSecretKey($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Channel whereShopId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Channel whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Channel whereUuid($value)
 * @mixin \Eloquent
 */
class Channel extends Model
{
    protected $table = 'channels';


}
