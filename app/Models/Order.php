<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Ramsey\Uuid\Uuid;

/**
 * App\Models\Order
 *
 * @property int $id
 * @property string $type
 * @property string $no
 * @property string|null $suffix
 * @property int $user_id
 * @property array $address
 * @property array|null $identity
 * @property float|null $freight 运费
 * @property float $product_amount 商品总额
 * @property float $total_amount 支付金额
 * @property float $cost_amount 成本总额
 * @property float $profit_amount 利润总额
 * @property string|null $remark
 * @property string|null $seller_remark
 * @property \Illuminate\Support\Carbon|null $paid_at
 * @property int|null $coupon_code_id
 * @property float|null $coupon_amount
 * @property float|null $discount_amount
 * @property float|null $tax_amount 税费
 * @property int|null $group_id
 * @property int|null $bargain_id
 * @property int|null $warehouse_id 仓库id
 * @property string|null $payment_method
 * @property string|null $payment_no
 * @property string $refund_status
 * @property string|null $refund_no
 * @property bool $closed
 * @property bool $reviewed
 * @property string $ship_status
 * @property array|null $ship_data
 * @property int|null $waybill_print
 * @property string $customs_status
 * @property string|null $customs_data
 * @property array|null $pay_data
 * @property array|null $pay_notify_data
 * @property array|null $extra
 * @property int|null $parent_id 拆单-上级id
 * @property int $split
 * @property tinyint $push_pay
 * @property \Illuminate\Support\Carbon|null $delivered_at
 * @property \Illuminate\Support\Carbon|null $received_at
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property \Illuminate\Support\Carbon|null $deleted_at
 * @property int $shop_id
 * @property-read \App\Models\UserCouponCode|null $couponCode
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\OrderCpItem[] $cpItems
 * @property-read int|null $cp_items_count
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\OrderCustomsLog[] $customsLogs
 * @property-read int|null $customs_logs_count
 * @property-read \App\Models\CustomsNoStatus $customsNoStatus
 * @property-read \App\Models\CustomsPay $customsPay
 * @property-read \App\Models\CustomsStatusDetail $customsStatusDetail
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\OrderItem[] $items
 * @property-read int|null $items_count
 * @property-read \App\Models\User $user
 * @property-read \App\Models\Warehouse|null $warehouse
 * @method static bool|null forceDelete()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Order newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Order newQuery()
 * @method static \Illuminate\Database\Query\Builder|\App\Models\Order onlyTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Order query()
 * @method static bool|null restore()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Order whereAddress($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Order whereBargainId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Order whereClosed($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Order whereCostAmount($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Order whereCouponAmount($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Order whereCouponCodeId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Order whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Order whereCustomsData($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Order whereCustomsStatus($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Order whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Order whereDeliveredAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Order whereDiscountAmount($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Order whereExtra($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Order whereFreight($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Order whereGroupId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Order whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Order whereIdentity($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Order whereNo($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Order wherePaidAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Order whereParentId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Order wherePayData($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Order wherePayNotifyData($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Order wherePaymentMethod($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Order wherePaymentNo($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Order whereProductAmount($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Order whereProfitAmount($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Order whereReceivedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Order whereRefundNo($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Order whereRefundStatus($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Order whereRemark($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Order whereReviewed($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Order whereSellerRemark($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Order whereShipData($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Order whereShipStatus($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Order whereShopId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Order whereSplit($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Order whereSuffix($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Order whereTaxAmount($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Order whereTotalAmount($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Order whereType($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Order whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Order whereUserId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Order whereWarehouseId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Order whereWaybillPrint($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\Order withTrashed()
 * @method static \Illuminate\Database\Query\Builder|\App\Models\Order withoutTrashed()
 * @mixin \Eloquent
 * @property int|null $split_number 拆单序号
 * @property int|null $import_flag
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Order whereImportFlag($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Order wherePushPay($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Order whereSplitNumber($value)
 * @property string $delivery_method
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Order whereDeliveryMethod($value)
 */
class Order extends Model
{
    use SoftDeletes;

    const REFUND_STATUS_PENDING = 'pending';
    const REFUND_STATUS_APPLIED = 'applied';
    const REFUND_STATUS_PROCESSING = 'processing';
    const REFUND_STATUS_SUCCESS = 'success';
    const REFUND_STATUS_FAILED = 'failed';

    const SHIP_STATUS_PENDING = 'pending';
    const SHIP_STATUS_PRINTED_PENDING = 'printed_pending';
    const SHIP_STATUS_DELIVERED = 'delivered';
    const SHIP_STATUS_RECEIVED = 'received';
    const SHIP_STATUS_FINISHED = 'finished';

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
    const TYPE_GROUP = 'group';
    const TYPE_BARGAIN = 'bargain';
    const TYPE_EXCHANGE = 'exchange';
    const TYPE_RECHARGE = 'recharge';
    const TYPE_SELECT = 'select';
    const TYPE_GIFT = 'gift';

    public static $refundStatusMap = [
        self::REFUND_STATUS_PENDING    => '未退款',
        self::REFUND_STATUS_APPLIED    => '已申请退款',
        self::REFUND_STATUS_PROCESSING => '退款中',
        self::REFUND_STATUS_SUCCESS    => '退款成功',
        self::REFUND_STATUS_FAILED     => '退款失败',
    ];

    public static $shipStatusMap = [
        self::SHIP_STATUS_PENDING         => '未发货',
        self::SHIP_STATUS_PRINTED_PENDING => '已打印待发货',
        self::SHIP_STATUS_DELIVERED       => '已发货',
        self::SHIP_STATUS_RECEIVED        => '已收货',
        self::SHIP_STATUS_FINISHED        => '已完成',
    ];

    public static $customsStatusMap = [
//        self::CUSTOMS_STATUS_PENDING           => '未清关',
        self::CUSTOMS_STATUS_PENDING           => '未推送',
        self::CUSTOMS_STATUS_PROCESSING        => '清关中',
//        self::CUSTOMS_STATUS_SUCCESS           => '清关成功',
        self::CUSTOMS_STATUS_SUCCESS           => '推送成功',
//        self::CUSTOMS_STATUS_FAILED            => '清关失败',
        self::CUSTOMS_STATUS_FAILED            => '推送失败',
        self::CUSTOMS_STATUS_CHARGEBACK        => '海关退单',
        self::CUSTOMS_STATUS_CANCEL_PROCESSING => '撤销中',
        self::CUSTOMS_STATUS_CANCEL_FAILED     => '撤销失败',
        self::CUSTOMS_STATUS_CANCEL_SUCCESS    => '撤销成功',
        self::CUSTOMS_STATUS_REFUND_PROCESSING => '退运中',
        self::CUSTOMS_STATUS_REFUND_FAILED     => '退运失败',
        self::CUSTOMS_STATUS_REFUND_SUCCESS    => '退运成功',
    ];

    public static $typeMap = [
        self::TYPE_NORMAL   => '普通商品',
        self::TYPE_GROUP    => '团购商品',
        self::TYPE_BARGAIN  => '砍价商品',
        self::TYPE_EXCHANGE => '兑换商品',
        self::TYPE_RECHARGE => '充值',
        self::TYPE_SELECT   => '预售',
        self::TYPE_GIFT     => '礼物',
    ];

    const DELIVERY_METHOD_GIFT = 'gift';
    const DELIVERY_METHOD_PICK_UP = 'pick_up';
    const DELIVERY_METHOD_EXPRESS = 'express';
    const DELIVERY_METHOD_INTEGRAL = 'integral';
    const DELIVERY_METHOD_FOOD_STAMP = 'food_stamp';

    public static $deliveryMethodMap = [
        self::DELIVERY_METHOD_GIFT       => '礼品库',
        self::DELIVERY_METHOD_PICK_UP    => '自提',
        self::DELIVERY_METHOD_EXPRESS    => '快递',
        self::DELIVERY_METHOD_INTEGRAL   => '兑换积分',
        self::DELIVERY_METHOD_FOOD_STAMP => '粮票',
    ];
    
    const AFTERSALES_STATUS_PENDING = 'pending';
    const AFTERSALES_STATUS_REFUND = 'refund';
    const AFTERSALES_STATUS_FOODSTAMP = 'foodstamp';
    const AFTERSALES_STATUS_REISSUE = 'reissue';
    
    public static $aftersalesStatusMap = [
        self::AFTERSALES_STATUS_PENDING    => '无售后',
        self::AFTERSALES_STATUS_REFUND    => '售后退款',
        self::AFTERSALES_STATUS_FOODSTAMP => '售后补粮票',
        self::AFTERSALES_STATUS_REISSUE    => '售后补发货',
    ];
    
    protected $guarded = ['id'];

    protected $casts = [
        'closed'          => 'boolean',
        'reviewed'        => 'boolean',
        'is_virtual'      => 'boolean',
        'address'         => 'json',
        'identity'        => 'json',
        'ship_data'       => 'json',
        'extra'           => 'json',
        'pay_data'        => 'json',
        'pay_notify_data' => 'json',
    ];

    protected $dates = [
        'paid_at',
        'deleted_at',
        'delivered_at',
        'received_at',
        'finished_at',
    ];

    protected static function boot()
    {
        parent::boot();
        // 监听模型创建事件，在写入数据库之前触发
        static::creating(function ($model) {
            // 如果模型的 no 字段为空
            if (!$model->no) {
                // 调用 findAvailableNo 生成订单流水号
                $model->no = static::findAvailableNo();
                // 如果生成失败，则终止创建订单
                if (!$model->no) {
                    return false;
                }
            }
            // 如果模型的 uuid 字段为空
            if (!$model->uuid) {
                // 调用 findAvailableUUid
                $model->uuid = static::getAvailableUuid();
                // 如果生成失败，则终止创建订单
                if (!$model->uuid) {
                    return false;
                }
            }
            // 创建时自动填充店铺id
            if (!$model->shop_id) {
                if (auth('api')->user()->shop_id) {
                    $model->shop_id = auth('api')->user()->shop_id;
                } else {
                    return false;
                }
            }
        });
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function warehouse()
    {
        return $this->belongsTo(Warehouse::class);
    }

    public function items()
    {
        return $this->hasMany(OrderItem::class);
    }

    public function gifts()
    {
        return $this->hasMany(UserGiftItem::class);
    }

    public function cpItems()
    {
        return $this->hasMany(OrderCpItem::class);
    }

    public function couponCode()
    {
        return $this->belongsTo(UserCouponCode::class, 'coupon_code_id', 'id');
    }

    public function customsLogs()
    {
        return $this->hasMany(OrderCustomsLog::class, 'order_id', 'id');
    }

    public function customsPay()
    {
        return $this->hasOne(CustomsPay::class, 'order_no', 'no');
    }

    public function customsNoStatus()
    {
        return $this->hasOne(CustomsNoStatus::class, 'order_no', 'no');
    }

    public function customsStatusDetail()
    {
        return $this->hasOne(CustomsStatusDetail::class, 'order_no', 'no');
    }

    public function cards()
    {
        return $this->hasMany(ProductSkuCard::class);
    }

    public function stores()
    {
        return $this->hasMany(SupplierStore::class, 'supplier_id', 'supplier_id');
    }

    public static function findAvailableNo()
    {
        // 订单流水号前缀
        $prefix = date('YmdHis');
        for ($i = 0; $i < 10; $i++) {
            // 随机生成 6 位的数字
            $no = $prefix . str_pad(random_int(0, 999999), 6, '0', STR_PAD_LEFT);
            // 判断是否已经存在
            if (!static::query()->where('no', $no)->exists()) {
                return $no;
            }
            usleep(100);
        }
        \Log::warning(sprintf('find order no failed'));

        return false;
    }

    public static function getAvailableRefundNo()
    {
        do {
            // Uuid类可以用来生成大概率不重复的字符串
            $no = Uuid::uuid4()->getHex();
            // 为了避免重复我们在生成之后在数据库中查询看看是否已经存在相同的退款订单号
        } while (self::query()->where('refund_no', $no)->exists());

        return $no;
    }

    public static function getAvailableUuid()
    {
        do {
            // Uuid类可以用来生成大概率不重复的字符串
            $uuid = Uuid::uuid4()->getHex();
        } while (self::query()->where('uuid', $uuid)->exists());

        return $uuid;
    }

    public function paymentCountDown($created_at)
    {
        if (!$created_at) return 0;
        $times = $created_at->timestamp + config('app.order_ttl');
        if (($times - Carbon::now()->timestamp) > 0) {
            return $times - Carbon::now()->timestamp;
        } else {
            return 0;
        }


    }

}
