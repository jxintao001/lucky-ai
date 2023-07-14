<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Str;
use Ramsey\Uuid\Uuid;


/**
 * App\Models\GiftPackage
 *
 * @property int $id
 * @property string $no 礼包编号hash
 * @property string $type 类型（单人-single_people 多人-many_people 多人整套-many_people_set）
 * @property string $title 礼包名称
 * @property int $user_id 用户ID
 * @property int $gift_count 礼品数量
 * @property int $gift_receive_count 礼品已领取数量
 * @property int $receive_limit 领取数量限制
 * @property string $wish_text 祝福文字
 * @property string $wish_image 祝福图片
 * @property string $wish_audio 祝福语音
 * @property string $wish_video 祝福视频
 * @property int $template_id 模版ID
 * @property string $wechat_group_id 微信群ID
 * @property string $question 问题
 * @property string $answer 答案
 * @property int $closed 是否关闭（1-是 0-否）
 * @property string|null $start_at 发礼包时间
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property string|null $deleted_at
 * @property int $shop_id
 * @property-read \App\Models\Order $order
 * @property-read \App\Models\Product $product
 * @property-read \App\Models\ProductSku $productSku
 * @property-read \App\Models\User $user
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\GiftPackage newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\GiftPackage newQuery()
 * @method static \Illuminate\Database\Query\Builder|\App\Models\GiftPackage onlyTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\GiftPackage query()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\GiftPackage whereAnswer($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\GiftPackage whereClosed($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\GiftPackage whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\GiftPackage whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\GiftPackage whereGiftCount($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\GiftPackage whereGiftReceiveCount($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\GiftPackage whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\GiftPackage whereNo($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\GiftPackage whereQuestion($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\GiftPackage whereReceiveLimit($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\GiftPackage whereShopId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\GiftPackage whereStartAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\GiftPackage whereTemplateId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\GiftPackage whereTitle($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\GiftPackage whereType($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\GiftPackage whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\GiftPackage whereUserId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\GiftPackage whereWechatGroupId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\GiftPackage whereWishAudio($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\GiftPackage whereWishImage($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\GiftPackage whereWishText($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\GiftPackage whereWishVideo($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\GiftPackage withTrashed()
 * @method static \Illuminate\Database\Query\Builder|\App\Models\GiftPackage withoutTrashed()
 * @mixin \Eloquent
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\OrderItem[] $items
 * @property-read int|null $items_count
 * @property int|null $set_count 套装数量
 * @property int $set_receive_count 套装已领取数量
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\GiftPackage whereSetCount($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\GiftPackage whereSetReceiveCount($value)
 * @property string $status 状态（正常-normal 过期-expired 领完-Finish）
 * @property string|null $finished_at 领完时间
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\GiftPackageReceiveItem[] $receiveItems
 * @property-read int|null $receive_items_count
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\GiftPackageReceive[] $receives
 * @property-read int|null $receives_count
 * @property-read \App\Models\GiftPackageTemplate $template
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\GiftPackage whereFinishedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\GiftPackage whereStatus($value)
 */
class GiftPackage extends Model
{
    use SoftDeletes;

    const TYPE_SINGLE_PEOPLE = 'single_people';
    const TYPE_MANY_PEOPLE = 'many_people';
    const TYPE_MANY_PEOPLE_SET = 'many_people_set';

    public static $typeMap = [
        self::TYPE_SINGLE_PEOPLE   => '单人',
        self::TYPE_MANY_PEOPLE     => '多人',
        self::TYPE_MANY_PEOPLE_SET => '多人整套',
    ];

    const STATUS_NORMAL = 'normal';
    const STATUS_EXPIRED = 'expired';
    const STATUS_FINISH = 'finish';

    public static $statusMap = [
        self::STATUS_NORMAL  => '正常',
        self::STATUS_EXPIRED => '过期',
        self::STATUS_FINISH  => '领完',
    ];

    protected $guarded = ['id'];

    protected $dates = [
        'start_at',
        'finished_at',
    ];

    protected static function boot()
    {
        parent::boot();
        static::creating(function ($model) {
            // 如果模型的 no 字段为空
            if (!$model->no) {
                // 调用 findAvailableNo 生成礼包编号
                $model->no = static::findAvailableNo();
                // 如果生成失败，则终止创建订单
                if (!$model->no) {
                    return false;
                }
            }
            // 如果模型的 code 字段为空
            if (!$model->code) {
                // 调用 findAvailableNo 生成礼包编号
                $model->code = static::findAvailableCode();
                // 如果生成失败，则终止创建订单
                if (!$model->code) {
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

    public static function findAvailableNo()
    {
        do {
            // Uuid类可以用来生成大概率不重复的字符串
            $no = Uuid::uuid4()->getHex();
        } while (self::query()->where('no', $no)->exists());

        return $no;
    }

    public static function findAvailableCode()
    {
        // 订单流水号前缀
        $prefix = date('YmdHis');
        for ($i = 0; $i < 10; $i++) {
            // 随机生成 6 位的数字
            $code = $prefix . str_pad(random_int(0, 999999), 6, '0', STR_PAD_LEFT);
            // 判断是否已经存在
            if (!static::query()->where('code', $code)->exists()) {
                return $code;
            }
            usleep(100);
        }
        \Log::warning(sprintf('find order no failed'));

        return false;
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function items()
    {
        return $this->hasMany(GiftPackageItem::class);
    }

    public function receives()
    {
        return $this->hasMany(GiftPackageReceive::class);
    }

    public function receiveItems()
    {
        return $this->hasMany(GiftPackageReceiveItem::class);
    }

    public function template()
    {
        return $this->belongsTo(GiftPackageTemplate::class)->withDefault();
    }

    public function sets()
    {
        return $this->hasMany(GiftPackageSet::class);
    }

    public function gifts()
    {
        return $this->hasMany(UserGiftItem::class, 'gift_package_id');
    }

    public function getWishImageAttribute($value)
    {
        if (!$value) {
            return '';
        }
        // 如果 image 字段本身就已经是完整的 url 就直接返回
        if (Str::startsWith($value, ['http://', 'https://'])) {
            return $value;
        }

        return config('api.img_host') . $value;
    }

    public function getWishImage2Attribute($value)
    {
        if (!$value) {
            return '';
        }
        // 如果 image 字段本身就已经是完整的 url 就直接返回
        if (Str::startsWith($value, ['http://', 'https://'])) {
            return $value;
        }

        return config('api.img_host') . $value;
    }

    public function getWishAudioAttribute($value)
    {
        if (!$value) {
            return '';
        }
        // 如果 image 字段本身就已经是完整的 url 就直接返回
        if (Str::startsWith($value, ['http://', 'https://'])) {
            return $value;
        }

        return config('api.img_host') . $value;
    }

    public function getWishVideoAttribute($value)
    {
        if (!$value) {
            return '';
        }
        // 如果 image 字段本身就已经是完整的 url 就直接返回
        if (Str::startsWith($value, ['http://', 'https://'])) {
            return $value;
        }

        return config('api.img_host') . $value;
    }

}
