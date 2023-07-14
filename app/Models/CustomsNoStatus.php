<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * App\Models\CustomsNoStatus
 *
 * @property string $order_no 订单号
 * @property string|null $logistics_type 物流类型
 * @property string|null $logistics_no 运单号
 * @property string|null $big_pen 大头笔
 * @property string $status 状态
 * @property string $create_date
 * @property string|null $update_date
 * @property string|null $remark 说明
 * @property string|null $logistics_img 二维码
 * @property string|null $print_flag 打印状态
 * @property string|null $cop_no
 * @property string|null $invt_no
 * @property string|null $pre_no
 * @property-read \App\Models\Order $order
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\CustomsNoStatus newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\CustomsNoStatus newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\CustomsNoStatus query()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\CustomsNoStatus whereBigPen($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\CustomsNoStatus whereCopNo($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\CustomsNoStatus whereCreateDate($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\CustomsNoStatus whereInvtNo($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\CustomsNoStatus whereLogisticsImg($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\CustomsNoStatus whereLogisticsNo($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\CustomsNoStatus whereLogisticsType($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\CustomsNoStatus whereOrderNo($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\CustomsNoStatus wherePreNo($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\CustomsNoStatus wherePrintFlag($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\CustomsNoStatus whereRemark($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\CustomsNoStatus whereStatus($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\CustomsNoStatus whereUpdateDate($value)
 * @mixin \Eloquent
 */
class CustomsNoStatus extends Model
{
    protected $table = 'customs_no_status';

    public function order()
    {
        return $this->belongsTo(Order::class,'order_no','no');
    }
}
