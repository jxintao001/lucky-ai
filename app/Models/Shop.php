<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * App\Models\Shop
 *
 * @property int $id
 * @property string|null $name
 * @property string|null $code
 * @property string|null $title
 * @property string|null $logo
 * @property string|null $qr_code 二维码
 * @property string|null $introduction
 * @property string|null $wechat_app_id
 * @property string|null $wechat_app_secret
 * @property int|null $fixed_rate 固定税率
 * @property string|null $banned_products 禁售商品
 * @property int|null $is_banned
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\User[] $users
 * @property-read int|null $users_count
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Shop newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Shop newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Shop query()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Shop whereBannedProducts($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Shop whereCode($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Shop whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Shop whereFixedRate($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Shop whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Shop whereIntroduction($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Shop whereIsBanned($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Shop whereLogo($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Shop whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Shop whereQrCode($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Shop whereTitle($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Shop whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Shop whereWechatAppId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Shop whereWechatAppSecret($value)
 * @mixin \Eloquent
 * @property int|null $disable_edit 禁止编辑
 * @property int|null $user_id
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Shop whereDisableEdit($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Shop whereUserId($value)
 */
class Shop extends Model
{
    const MASTER_SHOP_ID = 1;

    public function users()
    {
        return $this->hasMany(User::class);
    }

    public function getBannedProductsAttribute($value)
    {
        return array_values(json_decode($value, true) ?: []);
    }

}
