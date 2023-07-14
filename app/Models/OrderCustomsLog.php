<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * App\Models\OrderCustomsLog
 *
 * @property int $id
 * @property string $type 订单类型
 * @property int $order_id 订单id
 * @property string $order_no 订单号
 * @property string|null $push_data 推送数据
 * @property string|null $remark 备注
 * @property int|null $synced 是否已经同步
 * @property \Illuminate\Support\Carbon|null $created_at 创建时间
 * @property \Illuminate\Support\Carbon|null $updated_at 更新时间
 * @property string|null $deleted_at 删除时间
 * @property int $shop_id 店铺id
 * @property-read \App\Models\Order $order
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\OrderCustomsLog newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\OrderCustomsLog newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\OrderCustomsLog query()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\OrderCustomsLog whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\OrderCustomsLog whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\OrderCustomsLog whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\OrderCustomsLog whereOrderId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\OrderCustomsLog whereOrderNo($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\OrderCustomsLog wherePushData($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\OrderCustomsLog whereRemark($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\OrderCustomsLog whereShopId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\OrderCustomsLog whereSynced($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\OrderCustomsLog whereType($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\OrderCustomsLog whereUpdatedAt($value)
 * @mixin \Eloquent
 */
class OrderCustomsLog extends Model
{
    protected $table = 'order_customs_logs';
    protected $fillable = ['type', 'order_id', 'order_no', 'push_data', 'remark', 'synced', 'shop_id'];

    public function order()
    {
        return $this->belongsTo(Order::class);
    }
}
