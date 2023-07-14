<?php
namespace App\Models;

use Carbon\Carbon;
use Dingo\Api\Exception\ResourceException;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * App\Models\Bargain
 *
 * @property int $id
 * @property int $user_id
 * @property int|null $order_id
 * @property int $product_id
 * @property int $sku_id
 * @property float $target_price
 * @property float $current_price
 * @property float $price
 * @property int $user_count
 * @property string $status
 * @property bool $closed
 * @property \Illuminate\Support\Carbon|null $paid_at
 * @property \Illuminate\Support\Carbon|null $finished_at
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property \Illuminate\Support\Carbon|null $deleted_at
 * @property int $shop_id
 * @property-read \App\Models\BargainProduct $bargainProduct
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\BargainItem[] $items
 * @property-read int|null $items_count
 * @property-read \App\Models\Order|null $order
 * @property-read \App\Models\Product $product
 * @property-read \App\Models\ProductSku $productSku
 * @property-read \App\Models\User $user
 * @method static bool|null forceDelete()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Bargain newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Bargain newQuery()
 * @method static \Illuminate\Database\Query\Builder|\App\Models\Bargain onlyTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Bargain query()
 * @method static bool|null restore()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Bargain whereClosed($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Bargain whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Bargain whereCurrentPrice($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Bargain whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Bargain whereFinishedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Bargain whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Bargain whereOrderId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Bargain wherePaidAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Bargain wherePrice($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Bargain whereProductId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Bargain whereShopId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Bargain whereSkuId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Bargain whereStatus($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Bargain whereTargetPrice($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Bargain whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Bargain whereUserCount($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Bargain whereUserId($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\Bargain withTrashed()
 * @method static \Illuminate\Database\Query\Builder|\App\Models\Bargain withoutTrashed()
 * @mixin \Eloquent
 */
class Bargain extends Model
{
    use SoftDeletes;
    // 定义团购的 4 种状态
    const STATUS_PENDING = 'pending';
    const STATUS_WAITING = 'waiting';
    const STATUS_SUCCESS = 'success';
    const STATUS_FAIL = 'fail';

    public static $statusMap = [
        self::STATUS_PENDING => '待砍价',
        self::STATUS_WAITING => '砍价中',
        self::STATUS_SUCCESS => '砍价成功',
        self::STATUS_FAIL    => '砍价失败',
    ];
    protected $dates = ['paid_at','finished_at','deleted_at'];
    protected $fillable = [
        'user_id',
        'order_id',
        'product_id',
        'sku_id',
        'target_price',
        'current_price',
        'price',
        'user_count',
        'status',
        'closed',
        'paid_at',
        'finished_at',
        'shop_id',
    ];

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
        return $this->belongsTo(Order::class)->withDefault(function ($order) {
        });
    }

    public function product()
    {
        return $this->belongsTo(Product::class);
    }

    public function productSku()
    {
        return $this->belongsTo(ProductSku::class,'sku_id','id');
    }

    public function items()
    {
        return $this->hasMany(BargainItem::class);
    }

    public function bargainProduct()
    {
        return $this->belongsTo(BargainProduct::class,'product_id','product_id');
    }

    public function closeCountDown($created_at){
        if (!$created_at) return 0;
        $times = $created_at->timestamp+config('app.bargain_ttl');
        if (($times - Carbon::now()->timestamp) > 0){
            return $times - Carbon::now()->timestamp;
        }else{
            return 0;
        }


    }

    public function checkAvailable(User $user, Bargain $bargain)
    {
        if ($this->status == Bargain::STATUS_FAIL) {
            throw new ResourceException('该砍价已经关闭');
        }
        if ($this->status == Bargain::STATUS_SUCCESS) {
            throw new ResourceException('已经砍到了目标价格，不能在砍了');
        }
        if ($this->order_id) {
            throw new ResourceException('该砍价已经提交订单');
        }
        if ($this->paid_at) {
            throw new ResourceException('该砍价已经完成');
        }
        //DB::connection()->enableQueryLog(); // 开启查询日志
        $count = BargainItem::where('bargain_id', $bargain->id)
            ->where('user_id', $user->id)
            ->count();
        //print_r(DB::getQueryLog());exit();
        if ($count>0) {
            throw new ResourceException('已经砍过了，不能重复砍价');
        }
        if ($bargain->current_price <= $bargain->target_price) {
            throw new ResourceException('已经砍到了目标价格，不能在砍了');
        }

    }

    public function randomCutPrice($bargain)
    {
        $bargain_product = BargainProduct::where('product_id',$bargain->product_id)->first();
        $price  = $bargain->current_price - $bargain->target_price;
        if (($bargain->user_count+1) < $bargain_product->lower_limit){ // 大于参与人数下限
            $price = $price * 0.8;
        }elseif (($bargain->user_count+1) >= $bargain_product->upper_limit){ // 等于参与人数上限
            return $price;
        }
        if ($price <= 0) return 0;
        if ($price <= 0.01) return 0.01;
        $price = $price*100;
        $cut_price = mt_rand(1, $price)/100;
        return $cut_price;
    }

}
