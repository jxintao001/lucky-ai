<?php
namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * App\Models\GroupItem
 *
 * @property int $id
 * @property int|null $group_id
 * @property int $user_id
 * @property int $order_id
 * @property int $product_id
 * @property int $sku_id
 * @property string $status
 * @property int $is_head
 * @property bool $closed
 * @property \Illuminate\Support\Carbon|null $paid_at
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property \Illuminate\Support\Carbon|null $deleted_at
 * @property int $shop_id
 * @property-read \App\Models\Group|null $group
 * @property-read \App\Models\GroupProduct $groupProduct
 * @property-read \App\Models\Order $order
 * @property-read \App\Models\Product $product
 * @property-read \App\Models\ProductSku $productSku
 * @property-read \App\Models\User $user
 * @method static bool|null forceDelete()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\GroupItem newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\GroupItem newQuery()
 * @method static \Illuminate\Database\Query\Builder|\App\Models\GroupItem onlyTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\GroupItem query()
 * @method static bool|null restore()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\GroupItem whereClosed($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\GroupItem whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\GroupItem whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\GroupItem whereGroupId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\GroupItem whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\GroupItem whereIsHead($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\GroupItem whereOrderId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\GroupItem wherePaidAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\GroupItem whereProductId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\GroupItem whereShopId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\GroupItem whereSkuId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\GroupItem whereStatus($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\GroupItem whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\GroupItem whereUserId($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\GroupItem withTrashed()
 * @method static \Illuminate\Database\Query\Builder|\App\Models\GroupItem withoutTrashed()
 * @mixin \Eloquent
 */
class GroupItem extends Model
{
    use SoftDeletes;
    // 定义团购的 3 种状态
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
    protected $dates = ['paid_at','deleted_at'];
    protected $fillable = [
        'group_id',
        'user_id',
        'order_id',
        'product_id',
        'sku_id',
        'status',
        'is_head',
        'paid_at',
        'closed',
        'created_at',
        'updated_at',
        'shop_id',
    ];
    protected $casts = [
        'closed'    => 'boolean',
    ];
    //protected $dates = [ 'paid_at'];

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

    public function group()
    {
        return $this->belongsTo(Group::class);
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
        return $this->belongsTo(ProductSku::class,'sku_id','id');
    }

    public function groupProduct()
    {
        return $this->belongsTo(GroupProduct::class, 'product_id','product_id');
//        ->withDefault(function ($group_product) {
//        $group_product->id = null;
//        $group_product->product_id = null;
//        $group_product->price = null;
//        $group_product->target_count = null;
//        $group_product->begin_at = null;
//        $group_product->end_at = null;
//    })
    }

//    // 创建一个访问器，返回当前还款计划需还款的总金额
//    public function getTotalAttribute()
//    {
//        $total = big_number($this->base)->add($this->fee);
//        if (!is_null($this->fine)) {
//            $total->add($this->fine);
//        }
//
//        return $total->getValue();
//    }
//
//    // 创建一个访问器，返回当前还款计划是否已经逾期
//    public function getIsOverdueAttribute()
//    {
//        return Carbon::now()->gt($this->due_date);
//    }
}
