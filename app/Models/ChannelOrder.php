<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;


/**
 * App\Models\ChannelOrder
 *
 * @property int $id
 * @property string $type 订单类型
 * @property string $no 订单号
 * @property string|null $suffix
 * @property string|null $buyer_name 买家名称
 * @property array $push_data 推送数据
 * @property float $total_amount 支付金额
 * @property string|null $paid_at 支付时间
 * @property bool $closed 是否关闭
 * @property string $ship_status 快递状态
 * @property array|null $ship_data
 * @property string $customs_status 清关状态
 * @property string|null $customs_data
 * @property int|null $warehouse_id 仓库id
 * @property array|null $extra
 * @property int|null $waybill_print
 * @property \Illuminate\Support\Carbon|null $delivered_at
 * @property \Illuminate\Support\Carbon|null $received_at
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property \Illuminate\Support\Carbon|null $deleted_at
 * @property int $channel_id 渠道id
 * @property int $shop_id 店铺id
 * @property-read \App\Models\Channel $channel
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\ChannelOrder newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\ChannelOrder newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\ChannelOrder query()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\ChannelOrder whereBuyerName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\ChannelOrder whereChannelId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\ChannelOrder whereClosed($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\ChannelOrder whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\ChannelOrder whereCustomsData($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\ChannelOrder whereCustomsStatus($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\ChannelOrder whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\ChannelOrder whereDeliveredAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\ChannelOrder whereExtra($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\ChannelOrder whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\ChannelOrder whereNo($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\ChannelOrder wherePaidAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\ChannelOrder wherePushData($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\ChannelOrder whereReceivedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\ChannelOrder whereShipData($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\ChannelOrder whereShipStatus($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\ChannelOrder whereShopId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\ChannelOrder whereSuffix($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\ChannelOrder whereTotalAmount($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\ChannelOrder whereType($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\ChannelOrder whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\ChannelOrder whereWarehouseId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\ChannelOrder whereWaybillPrint($value)
 * @mixin \Eloquent
 */
class ChannelOrder extends Model
{
    protected $table = 'channel_orders';

    const REFUND_STATUS_PENDING = 'pending';
    const REFUND_STATUS_APPLIED = 'applied';
    const REFUND_STATUS_PROCESSING = 'processing';
    const REFUND_STATUS_SUCCESS = 'success';
    const REFUND_STATUS_FAILED = 'failed';

    const SHIP_STATUS_PENDING = 'pending';
    const SHIP_STATUS_PRINTED_PENDING = 'printed_pending';
    const SHIP_STATUS_DELIVERED = 'delivered';
    const SHIP_STATUS_RECEIVED = 'received';

    const CUSTOMS_STATUS_PENDING = 'pending';
    const CUSTOMS_STATUS_PROCESSING = 'processing';
    const CUSTOMS_STATUS_SUCCESS = 'success';
    const CUSTOMS_STATUS_FAILED = 'failed';
    const CUSTOMS_STATUS_CHARGEBACK = 'chargeback';
    const CUSTOMS_STATUS_CANCEL_PROCESSING = 'cancel_processing';
    const CUSTOMS_STATUS_CANCEL_FAILED = 'cancel_failed';
    const CUSTOMS_STATUS_CANCEL_SUCCESS = 'cancel_success';
    const CUSTOMS_STATUS_REFUND_PROCESSING = 'refund_processing';
    const CUSTOMS_STATUS_REFUND_FAILED = 'refund_failed';
    const CUSTOMS_STATUS_REFUND_SUCCESS = 'refund_success';

    const TYPE_NORMAL = 'normal';

    public static $refundStatusMap = [
        self::REFUND_STATUS_PENDING => '未退款',
        self::REFUND_STATUS_APPLIED => '已申请退款',
        self::REFUND_STATUS_PROCESSING => '退款中',
        self::REFUND_STATUS_SUCCESS => '退款成功',
        self::REFUND_STATUS_FAILED => '退款失败',
    ];

    public static $shipStatusMap = [
        self::SHIP_STATUS_PENDING => '未发货',
        self::SHIP_STATUS_PRINTED_PENDING => '已打印待发货',
        self::SHIP_STATUS_DELIVERED => '已发货',
        self::SHIP_STATUS_RECEIVED => '已收货',
    ];

    public static $customsStatusMap = [
        self::CUSTOMS_STATUS_PENDING => '未清关',
        self::CUSTOMS_STATUS_PROCESSING => '清关中',
        self::CUSTOMS_STATUS_SUCCESS => '清关成功',
        self::CUSTOMS_STATUS_FAILED => '清关失败',
        self::CUSTOMS_STATUS_CHARGEBACK => '海关退单',
        self::CUSTOMS_STATUS_CANCEL_PROCESSING => '撤销中',
        self::CUSTOMS_STATUS_CANCEL_FAILED => '撤销失败',
        self::CUSTOMS_STATUS_CANCEL_SUCCESS => '撤销成功',
        self::CUSTOMS_STATUS_REFUND_PROCESSING => '退运中',
        self::CUSTOMS_STATUS_REFUND_FAILED => '退运失败',
        self::CUSTOMS_STATUS_REFUND_SUCCESS => '退运成功',
    ];

    public static $typeMap = [
        self::TYPE_NORMAL => '普通订单',
    ];

    protected $fillable = [
        'type',
        'no',
        'suffix',
        'push_data',
        'closed',
        'ship_status',
        'ship_data',
        'waybill_print',
        'customs_status',
        'customs_data',
        'extra',
        'delivered_at',
        'received_at',
        'created_at',
        'updated_at',
        'deleted_at',
        'channel_id',
    ];

    protected $casts = [
        'closed' => 'boolean',
        'ship_data' => 'json',
        'extra' => 'json',
        'push_data' => 'json',
    ];

    protected $dates = [
        'deleted_at',
        'delivered_at',
        'received_at',
    ];


    public function channel()
    {
        return $this->belongsTo(Channel::class);
    }


}
