<?php

namespace App\Models;

use Dingo\Api\Exception\ResourceException;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Carbon\Carbon;


/**
 * App\Models\CouponCode
 *
 * @property int $id
 * @property string $name
 * @property string $code
 * @property string|null $cover
 * @property string|null $tips_image
 * @property string $type
 * @property string $use_type
 * @property int|null $target_id
 * @property float $value
 * @property int $total
 * @property int $received
 * @property int $used
 * @property float $min_amount
 * @property \Illuminate\Support\Carbon|null $not_before
 * @property \Illuminate\Support\Carbon|null $not_after
 * @property int|null $limit_receive
 * @property string|null $introduction
 * @property bool $enabled
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property int $shop_id
 * @property-read mixed $description
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\UserCouponCode[] $userCouponCodes
 * @property-read int|null $user_coupon_codes_count
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\CouponCode newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\CouponCode newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\CouponCode query()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\CouponCode whereCode($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\CouponCode whereCover($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\CouponCode whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\CouponCode whereEnabled($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\CouponCode whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\CouponCode whereIntroduction($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\CouponCode whereLimitReceive($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\CouponCode whereMinAmount($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\CouponCode whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\CouponCode whereNotAfter($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\CouponCode whereNotBefore($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\CouponCode whereReceived($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\CouponCode whereShopId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\CouponCode whereTargetId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\CouponCode whereTipsImage($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\CouponCode whereTotal($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\CouponCode whereType($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\CouponCode whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\CouponCode whereUseType($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\CouponCode whereUsed($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\CouponCode whereValue($value)
 * @mixin \Eloquent
 */
class CouponCode extends Model
{
    // 用常量的方式定义支持的优惠券类型
    const TYPE_FIXED = 'fixed';
    const TYPE_PERCENT = 'percent';

    public static $typeMap = [
        self::TYPE_FIXED   => '固定金额',
        self::TYPE_PERCENT => '比例',
    ];

    // 用常量的方式定义支持使用的规则
    const USE_TYPE_NORMAL = 'normal';
    const USE_TYPE_NEW_USER = 'new_user';
    //const USE_TYPE_ACTIVITY = 'activity';
    const USE_TYPE_INVITE = 'invite';
    const USE_TYPE_ASSIGN_PRODUCT = 'assign_product';

    public static $useTypeMap = [
        self::USE_TYPE_NORMAL   => '普通券',
        self::USE_TYPE_NEW_USER => '新人券',
        //self::USE_TYPE_ACTIVITY => '活动券',
        self::USE_TYPE_INVITE => '邀请券',
        self::USE_TYPE_ASSIGN_PRODUCT => '指定商品',
    ];

    protected $fillable = [
        'name',
        'code',
        'cover',
        'tips_image',
        'type',
        'use_type',
        'target_id',
        'value',
        'total',
        'received',
        'used',
        'min_amount',
        'not_before',
        'not_after',
        'limit_receive',
        'introduction',
        'enabled',
    ];
    protected $casts = [
        'enabled'   => 'boolean',
    ];
    // 指明这两个字段是日期类型
    protected $dates = ['not_before', 'not_after'];

    protected $appends = ['description'];

    public function getDescriptionAttribute()
    {
        $str = '';

        if ($this->min_amount > 0) {
            $str = '满'.str_replace('.00', '', $this->min_amount);
        }
        if ($this->type === self::TYPE_PERCENT) {
            return $str.'优惠'.str_replace('.00', '', $this->value).'%';
        }

        return $str.'减'.str_replace('.00', '', $this->value);
    }

    public function checkAvailable(User $user, $orderAmount = null)
    {
        if (!$this->enabled) {
            throw new ResourceException('优惠券不存在');
        }

        if ($this->not_before && $this->not_before->gt(Carbon::now())) {
            throw new ResourceException('该优惠券现在还不能使用');
        }

        if ($this->not_after && $this->not_after->lt(Carbon::now())) {
            throw new ResourceException('该优惠券已过期');
        }

        if (!is_null($orderAmount) && $orderAmount < $this->min_amount) {
            throw new ResourceException('订单金额不满足该优惠券最低金额');
        }

        if ($this->pivot->used) {
            throw new ResourceException('你已经使用过这张优惠券了');
        }
    }

    public function checkReceive(User $user)
    {
        if (!$this->enabled) {
            throw new ResourceException('优惠券不存在');
        }

        if ($this->shop_id != $user->shop_id) {
            throw new ResourceException('操作异常');
        }

        if ($this->total - $this->received <= 0) {
            throw new ResourceException('该优惠券已被领完');
        }

//        if ($this->not_before && $this->not_before->gt(Carbon::now())) {
//            throw new ResourceException('该优惠券现在还不能使用');
//        }

        if ($this->not_after && $this->not_after->lt(Carbon::now())) {
            throw new ResourceException('该优惠券已过期');
        }

        //DB::connection()->enableQueryLog(); // 开启查询日志
        $count = UserCouponCode::where('user_id', $user->id)
            ->where('coupon_code_id', $this->id)
            ->count();
        //print_r(DB::getQueryLog());exit();
        if ($count >= $this->limit_receive) {
            throw new ResourceException('每人只能领取'.$this->limit_receive.'张哦');
        }
    }

    public function getAdjustedPrice($orderAmount)
    {
        // 固定金额
        if ($this->type === self::TYPE_FIXED) {
            // 为了保证系统健壮性，我们需要订单金额最少为 0.01 元
            return max(0, $orderAmount - $this->value);
        }

        return number_format($orderAmount * (100 - $this->value) / 100, 2, '.', '');
    }

    public function changeUsed($increase = true)
    {
        // 传入 true 代表新增用量，否则是减少用量
        if ($increase) {
            // 与检查 SKU 库存类似，这里需要检查当前用量是否已经超过总量
            return $this->newQuery()->where('id', $this->id)->where('used', '<', $this->total)->increment('used');
        } else {
            return $this->decrement('used');
        }
    }

    public function changeReceived($increase = true)
    {
        // 传入 true 代表新增用量，否则是减少用量
        if ($increase) {
            // 与检查 SKU 库存类似，这里需要检查当前用量是否已经超过总量
            return $this->newQuery()->where('id', $this->id)->where('received', '<', $this->total)->increment('received');
        } else {
            return $this->decrement('received');
        }
    }

    public static function findAvailableCode($length = 16)
    {
        do {
            // 生成一个指定长度的随机字符串，并转成大写
            $code = strtoupper(Str::random($length));
            // 如果生成的码已存在就继续循环
        } while (self::query()->where('code', $code)->exists());

        return $code;
    }

    public function getBgColor($value)
    {
        if ($value <= 20){
            return 'blue';
        }elseif ($value <= 50){
            return 'red';
        }else{
            return 'yellow';
        }
    }

    public function userCouponCodes()
    {
        return $this->hasMany(UserCouponCode::class);
    }

}
