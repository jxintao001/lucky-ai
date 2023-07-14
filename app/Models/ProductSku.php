<?php
/**
 * Created by PhpStorm.
 * User: jiaoxintao
 * Date: 2018/10/29
 * Time: 3:47 PM
 */

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Str;


class ProductSku extends Model
{
    use SoftDeletes;

    const TYPE_ALL = 'all';
    const TYPE_ONLINE = 'online';
    const TYPE_OFFLINE = 'offline';

    public static $typeMap = [
        self::TYPE_ALL     => '全部',
        self::TYPE_ONLINE  => '快递',
        self::TYPE_OFFLINE => '自提',
    ];

    protected $fillable = ['title', 'no', 'description', 'original_price', 'price', 'stock', 'is_presale', 'presale', 'deliver_at'];

    protected $casts = [
        'is_presale' => 'boolean',
    ];

    protected $dates = [
        'deliver_at',
    ];

    /**
     *模型的「启动」方法.
     *
     * @return void
     */
    protected static function boot()
    {
        parent::boot();

        static::addGlobalScope('productSkuSub', function (Builder $builder) {
            $builder->with('productSkuSub');
        });
    }

    public function product()
    {

        return $this->belongsTo(Product::class);
    }

    public function group()
    {
        return $this->belongsTo(GroupProduct::class, 'product_id', 'product_id');
    }

    public function bargain()
    {
        return $this->belongsTo(BargainProduct::class, 'product_id', 'product_id');
    }

    public function productSkuSub()
    {
        $shop_id = intval(request('shop_id', 0));
        return $this->hasOne(ProductSkuSub::class)
            ->where(['product_sku_subs.shop_id' => $shop_id]);
    }

    public function decreaseStock($amount)
    {
        if ($amount < 0) {
            throw new InternalException('减库存不可小于0');
        }

        return $this->newQuery()->where('id', $this->id)->where('stock', '>=', $amount)->decrement('stock', $amount);
    }

    public function addStock($amount)
    {
        if ($amount < 0) {
            throw new InternalException('加库存不可小于0');
        }
        $this->increment('stock', $amount);
    }

    public function decreasePresale($amount)
    {
        if ($amount < 0) {
            throw new InternalException('减库存不可小于0');
        }

        return $this->newQuery()->where('id', $this->id)->where('presale', '>=', $amount)->decrement('presale', $amount);
    }

    public function getPriceAttribute($value)
    {
        return $this->productSkuSub !== null ? $this->productSkuSub->tax_price : $value;

    }

    public function getClubTaxPriceAttribute($value)
    {
        return $value >= 1 ? $value : $this->tax_price;
    }

    public function getTaxPriceAttribute($value)
    {
        if (auth('api')->id()) {
            if (auth('api')->user()->level == 0) {
                return $value;
            } elseif (auth('api')->user()->level == 1) {
                return $this->member_price;
            } elseif (auth('api')->user()->level == 2) {
                return $this->club_price;
            }
        } else {
            return $this->productSkuSub !== null ? $this->productSkuSub->tax_price : $value;
        }
//        return $this->productSkuSub !== null ? $this->productSkuSub->tax_price : $value;

    }

    public function getLimitBuyAttribute()
    {
        return $this->getOriginal('limit_num') == 0 ? 0 : 1;
    }

    public function getLimitNumAttribute($value)
    {
        // 限购数0（不限购）直接返回库存数
        if ($value == 0) {
            return $this->stock;
        }
        // 未登录直接返回限购数
        if (!auth('api')->id() || !auth('api')->user() || !auth('api')->user()->shop_id) {
            return $value;
        }
        $boughtCount = OrderItem::query()
            ->where('product_sku_id', $this->id)
            ->where('shop_id', auth('api')->user()->shop_id)
            ->whereHas('order', function ($query) {
                $query->where('user_id', auth('api')->id())
                    ->where('created_at', '>=', date('Y-m-d', strtotime('-3 day')))
                    ->where(function ($query) {
                        $query->where(function ($query) {
                            $query->whereNotNull('paid_at')
                                ->where('refund_status', '<>', Order::REFUND_STATUS_SUCCESS);
                        })->orWhere(function ($query) {
                            $query->where('closed', 0)
                                ->whereNull('paid_at');
                        });
                    });
            })->sum('amount');
        return $value - $boughtCount > 0 ? $value - $boughtCount : 0;
    }

    public function getTaxRateAttribute($value)
    {
        $shop_id = intval(request('shop_id', 0));
        $shop = Shop::find($shop_id);
        return $shop && $shop->fixed_rate == 1 && $value != 0 ? 9.1 : $value;

    }

    public function getCalculateTax($price)
    {
        return number_format($price * ($this->tax_rate / 100), 2, '.', '');
    }

    public function getShareImageAttribute($value)
    {
        if (!$value){
            return '';
        }
        // 如果 image 字段本身就已经是完整的 url 就直接返回
        if (Str::startsWith($value, ['http://', 'https://'])) {
            return $value;
        }

        return config('api.img_host') . $value;
    }

}