<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;


/**
 * App\Models\ChannelOrderLog
 *
 * @property int $id
 * @property string $type 订单类型
 * @property string $no 订单号
 * @property string|null $suffix
 * @property array $push_data 推送数据
 * @property string $sign 签名
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property string|null $deleted_at
 * @property int $channel_id 渠道id
 * @property int $shop_id 店铺id
 * @property-read \App\Models\Channel $channel
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\ChannelOrderLog newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\ChannelOrderLog newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\ChannelOrderLog query()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\ChannelOrderLog whereChannelId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\ChannelOrderLog whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\ChannelOrderLog whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\ChannelOrderLog whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\ChannelOrderLog whereNo($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\ChannelOrderLog wherePushData($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\ChannelOrderLog whereShopId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\ChannelOrderLog whereSign($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\ChannelOrderLog whereSuffix($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\ChannelOrderLog whereType($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\ChannelOrderLog whereUpdatedAt($value)
 * @mixin \Eloquent
 */
class ChannelOrderLog extends Model
{
    protected $table = 'channel_order_logs';

    protected $fillable = [
        'type',
        'no',
        'suffix',
        'push_data',
        'channel_id',
        'sign',
        'created_at',
        'updated_at',
        'deleted_at',
    ];

    protected $casts = [
        'push_data' => 'json',
    ];


    public function channel()
    {
        return $this->belongsTo(Channel::class);
    }


}
