<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * App\Models\CustomsStatusDetail
 *
 * @property string $order_no 运单号
 * @property string $logistics_status 推送物流状态
 * @property string $order_status 推送订单状态
 * @property string $pay_status 支付推送状态
 * @property string $inventory_status 清单推送状态
 * @property-read \App\Models\Order $order
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\CustomsStatusDetail newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\CustomsStatusDetail newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\CustomsStatusDetail query()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\CustomsStatusDetail whereInventoryStatus($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\CustomsStatusDetail whereLogisticsStatus($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\CustomsStatusDetail whereOrderNo($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\CustomsStatusDetail whereOrderStatus($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\CustomsStatusDetail wherePayStatus($value)
 * @mixin \Eloquent
 */
class CustomsStatusDetail extends Model
{
    protected $table = 'customs_status_detail';

    public function order()
    {
        return $this->belongsTo(Order::class,'order_no','no');
    }
}
