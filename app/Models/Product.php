<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * App\Models\Product
 *
 * @property int $id
 * @property string $type 商品类型
 * @property int|null $brand_id 品牌id
 * @property int|null $category_id 分类id
 * @property int|null $warehouse_id 仓库id
 * @property string $title 标题
 * @property string|null $long_title 长标题
 * @property string|null $detail 详情
 * @property string|null $cover 封面图
 * @property bool $on_sale 是否上架
 * @property int $is_banned 是否屏蔽
 * @property float $rating 评分
 * @property int $sold_count 销量
 * @property int $stock_count 库存
 * @property int $review_count 点击量
 * @property float $original_price 原价
 * @property float $price 售价
 * @property float $member_price 会员价
 * @property float $club_price club价
 * @property float|null $freight 运费
 * @property float|null $tax_rate 税率
 * @property int|null $package_limit 包裹数量限制
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property \Illuminate\Support\Carbon|null $deleted_at
 * @property int $shop_id 店铺id
 * @property-read \App\Models\BargainProduct $bargain
 * @property-read \App\Models\Brand|null $brand
 * @property-read \App\Models\Category|null $category
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\CombinationProduct[] $combination_products
 * @property-read int|null $combination_products_count
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\ProductDetailImage[] $detailImages
 * @property-read int|null $detail_images_count
 * @property-read \App\Models\GroupProduct $group
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\ProductImage[] $images
 * @property-read int|null $images_count
 * @property-read \App\Models\ProductSub $productSub
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\ProductProperty[] $properties
 * @property-read int|null $properties_count
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\ProductSku[] $skus
 * @property-read int|null $skus_count
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\Tag[] $tags
 * @property-read int|null $tags_count
 * @property-read \App\Models\Bargain $userBargain
 * @property-read \App\Models\Warehouse|null $warehouse
 * @method static bool|null forceDelete()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Product newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Product newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Product onSale()
 * @method static \Illuminate\Database\Query\Builder|\App\Models\Product onlyTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Product query()
 * @method static bool|null restore()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Product whereBrandId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Product whereCategoryId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Product whereCover($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Product whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Product whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Product whereDetail($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Product whereFreight($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Product whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Product whereIsBanned($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Product whereLongTitle($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Product whereOnSale($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Product whereOriginalPrice($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Product wherePackageLimit($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Product wherePrice($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\ProductSku whereMemberPrice($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\ProductSku whereClubPrice($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Product whereRating($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Product whereReviewCount($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Product whereShopId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Product whereSoldCount($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Product whereStockCount($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Product whereTaxRate($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Product whereTitle($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Product whereType($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Product whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Product whereWarehouseId($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\Product withTrashed()
 * @method static \Illuminate\Database\Query\Builder|\App\Models\Product withoutTrashed()
 * @mixin \Eloquent
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\Act[] $acts
 * @property-read int|null $acts_count
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\Brand[] $brands
 * @property-read int|null $brands_count
 * @property int|null $supplier_id 供应商ID
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\Season[] $seasons
 * @property-read int|null $seasons_count
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Product whereSupplierId($value)
 */
class Product extends Model
{
    use SoftDeletes;

    const TYPE_NORMAL = 'normal';
    const TYPE_GROUP = 'group';
    const TYPE_BARGAIN = 'bargain';
    const TYPE_COMBINATION = 'combination';
    const TYPE_SELECT = 'select';
    public static $typeMap = [
        self::TYPE_NORMAL      => '普通商品',
        self::TYPE_GROUP       => '团购商品',
        self::TYPE_BARGAIN     => '砍价商品',
        self::TYPE_COMBINATION => '组合商品',
        self::TYPE_SELECT      => '精选商品',
    ];

    protected $fillable = [
        'title',
        'long_title',
        'description',
        'cover',
        'images',
        'on_sale',
        'rating',
        'sold_count',
        'stock_count',
        'review_count',
        'original_price',
        'price',
        'type',
    ];

    protected $casts = [
        'on_sale' => 'boolean', // on_sale 是一个布尔类型的字段
    ];

    protected $dates = [
        'deleted_at',
        'select_end_at',
    ];

    /**
     *模型的「启动」方法.
     *
     * @return void
     */
    protected static function boot()
    {
        parent::boot();

        static::addGlobalScope('productSub', function (Builder $builder) {
            $builder->with('productSub');
        });
    }

    public function skus()
    {
        return $this->hasMany(ProductSku::class);
    }

    public function images()
    {
        return $this->hasMany(ProductImage::class);
    }

    public function detailImages()
    {
        return $this->hasMany(ProductDetailImage::class);
    }

    public function category()
    {
        return $this->belongsTo(Category::class)->withDefault();
    }

    public function warehouse()
    {
        return $this->belongsTo(Warehouse::class)->withDefault();
    }

    public function brand()
    {
        return $this->belongsTo(Brand::class)->withDefault();
    }

    public function brands()
    {
        return $this->morphToMany(Brand::class, 'brandable');
    }

    public function acts()
    {
        return $this->morphToMany(Act::class, 'actable');
    }

    public function tags()
    {
        return $this->morphToMany(Tag::class, 'taggable');
    }

    public function seasons()
    {
        return $this->morphToMany(Season::class, 'seasonable')->withTimestamps();
    }

    public function reports()
    {
        return $this->morphToMany(Report::class, 'reportable');
    }

    public function properties()
    {
        return $this->hasMany(ProductProperty::class);
    }

    public function group()
    {
        return $this->hasOne(GroupProduct::class);
    }

    public function bargain()
    {
        return $this->hasOne(BargainProduct::class);
    }

    public function userBargain()
    {
        return $this->hasOne(Bargain::class)->where('user_id', auth('api')->id())->where('status', Bargain::STATUS_WAITING)->withDefault(function ($order) {
        });
    }

    public function productSub()
    {
        $shop_id = intval(request('shop_id', 0));
        return $this->hasOne(ProductSub::class)
            ->where(['product_subs.shop_id' => $shop_id]);
    }

    public function combination_products()
    {
        return $this->hasMany(CombinationProduct::class);
    }

    public function getOriginalPriceAttribute($value)
    {
        return $this->productSub !== null ? $this->productSub->original_price : $value;
    }

    public function getPriceAttribute($value)
    {
//        return $this->productSub !== null ? $this->productSub->price : $value;
        if (auth('api')->id()) {
            $skus = $this->skus;
            if (auth('api')->user()->level == 0) {
                return collect($skus)->min('tax_price');
            }elseif (auth('api')->user()->level == 1) {
                return collect($skus)->min('member_price');

            } elseif (auth('api')->user()->level == 2) {
                return collect($skus)->min('club_price');
            }
        } else {
            return $this->productSub !== null ? $this->productSub->price : $value;
        }
    }

    public function getOnSaleAttribute($value)
    {
        return ($this->productSub !== null && $value === 1) ? $this->productSub->on_sale : $value;
    }

    public function getSelectCountdownDaysAttribute($value)
    {
        if (empty($this->select_end_at) || empty($this->select_end_at->timestamp)) {
            return 10;
        }
        return ($this->select_end_at->timestamp - time()) > 0 ? ceil(($this->select_end_at->timestamp - time()) / 86400) : 0;

    }

    public function scopeOnSale($query)
    {
        return $query->where('on_sale', 1)->where(function ($query) {
            $query->doesntHave('productSub')
                ->orWhereHas('productSub', function ($query) {
                    $query->where('on_sale', 1);
                });
        });
    }
}
