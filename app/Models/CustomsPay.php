<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * App\Models\CustomsPay
 *
 * @property int $id
 * @property string $order_no 订单号
 * @property string|null $pay_data 支付信息
 * @property \Illuminate\Support\Carbon|null $created_at 创建时间
 * @property \Illuminate\Support\Carbon|null $updated_at 更新时间
 * @property-read \App\Models\Order $order
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\CustomsPay newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\CustomsPay newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\CustomsPay query()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\CustomsPay whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\CustomsPay whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\CustomsPay whereOrderNo($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\CustomsPay wherePayData($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\CustomsPay whereUpdatedAt($value)
 * @mixin \Eloquent
 */
class CustomsPay extends Model
{
    protected $table = 'customs_pay';
    protected $fillable = ['order_no', 'pay_data'];
    //public $timestamps = false;

//    protected $casts = [
//        'pay_data' => 'json',
//    ];

    public function order()
    {
        return $this->belongsTo(Order::class, 'order_no', 'no');
    }
}
