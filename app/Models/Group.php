<?php
namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * App\Models\Group
 *
 * @property int $id
 * @property int $user_id
 * @property int $order_id
 * @property int $product_id
 * @property int $sku_id
 * @property int $target_count
 * @property int $user_count
 * @property string $status
 * @property bool $closed
 * @property \Illuminate\Support\Carbon|null $paid_at
 * @property \Illuminate\Support\Carbon|null $finished_at
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property \Illuminate\Support\Carbon|null $deleted_at
 * @property int $shop_id
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\GroupItem[] $items
 * @property-read int|null $items_count
 * @property-read \App\Models\Order $order
 * @property-read \App\Models\Product $product
 * @property-read \App\Models\ProductSku $productSku
 * @property-read \App\Models\User $user
 * @method static bool|null forceDelete()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Group newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Group newQuery()
 * @method static \Illuminate\Database\Query\Builder|\App\Models\Group onlyTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Group query()
 * @method static bool|null restore()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Group whereClosed($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Group whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Group whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Group whereFinishedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Group whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Group whereOrderId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Group wherePaidAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Group whereProductId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Group whereShopId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Group whereSkuId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Group whereStatus($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Group whereTargetCount($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Group whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Group whereUserCount($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Group whereUserId($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\Group withTrashed()
 * @method static \Illuminate\Database\Query\Builder|\App\Models\Group withoutTrashed()
 * @mixin \Eloquent
 */
class Group extends Model
{
    use SoftDeletes;
    // 定义团购的 4 种状态
    const STATUS_PENDING = 'pending';
    const STATUS_WAITING = 'waiting';
    const STATUS_SUCCESS = 'success';
    const STATUS_FAIL = 'fail';

    public static $statusMap = [
        self::STATUS_PENDING => '待付款',
        self::STATUS_WAITING => '团购中',
        self::STATUS_SUCCESS => '团购成功',
        self::STATUS_FAIL    => '团购失败',
    ];
    protected $dates = ['paid_at','finished_at','deleted_at'];
    protected $fillable = ['user_id', 'order_id', 'product_id', 'sku_id', 'target_count', 'user_count','paid_at','finished_at','status','closed', 'shop_id'];

    protected $casts = [
        'closed'    => 'boolean',
    ];

    protected static function boot()
    {
        parent::boot();
        static::creating(function ($model) {
            // 创建时自动填充店铺id
            if (auth('api')->user()->shop_id){
                $model->shop_id = auth('api')->user()->shop_id;
            } else {
                return false;
            }
        });
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function order()
    {
        return $this->belongsTo(Order::class);
    }

    public function product()
    {
        return $this->belongsTo(Product::class);
    }

    public function productSku()
    {
        return $this->belongsTo(ProductSku::class);
    }

    public function items()
    {
        return $this->hasMany(GroupItem::class)->where('status', GroupItem::STATUS_WAITING);
    }

    public function closeCountDown($paid_at){
        if (!$paid_at) return 0;
        $times = $paid_at->timestamp+config('app.group_ttl');
        if (($times - Carbon::now()->timestamp) > 0){
            return $times - Carbon::now()->timestamp;
        }else{
            return 0;
        }


    }

}
