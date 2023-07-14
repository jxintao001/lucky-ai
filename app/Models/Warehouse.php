<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * App\Models\Warehouse
 *
 * @property int $id
 * @property string|null $type
 * @property string|null $name
 * @property string|null $code
 * @property string|null $title
 * @property string|null $logo
 * @property int|null $express_id 快递id
 * @property string|null $introduction
 * @property int|null $is_banned
 * @property string|null $sender_province
 * @property string|null $sender_city
 * @property string|null $sender_district
 * @property string|null $sender_address
 * @property string|null $sender_name
 * @property string|null $sender_mobile
 * @property string|null $sender_telephone
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property int|null $shop_id
 * @property-read \App\Models\Expresses|null $express
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\FreightRule[] $freightRules
 * @property-read int|null $freight_rules_count
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Warehouse newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Warehouse newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Warehouse query()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Warehouse whereCode($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Warehouse whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Warehouse whereExpressId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Warehouse whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Warehouse whereIntroduction($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Warehouse whereIsBanned($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Warehouse whereLogo($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Warehouse whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Warehouse whereSenderAddress($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Warehouse whereSenderCity($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Warehouse whereSenderDistrict($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Warehouse whereSenderMobile($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Warehouse whereSenderName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Warehouse whereSenderProvince($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Warehouse whereSenderTelephone($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Warehouse whereShopId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Warehouse whereTitle($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Warehouse whereType($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Warehouse whereUpdatedAt($value)
 * @mixin \Eloquent
 */
class Warehouse extends Model
{

    public function freightRules()
    {
        return $this->hasMany(FreightRule::class);
    }

    public function express()
    {
        return $this->belongsTo(Expresses::class);
    }

}
