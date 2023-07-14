<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;


/**
 * App\Models\GiftPackageSet
 *
 * @property int $id
 * @property int|null $gift_package_id 礼品包ID
 * @property int $user_id 用户ID
 * @property int|null $product_id 商品ID
 * @property int|null $product_sku_id sku ID
 * @property int|null $amount 礼品数量
 * @property string $sku_title sku 标题
 * @property string $sku_image sku 图片
 * @property float|null $sku_price sku 价格
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property string|null $deleted_at
 * @property int $shop_id
 * @property-read \App\Models\GiftPackage $package
 * @property-read \App\Models\Product|null $product
 * @property-read \App\Models\ProductSku|null $productSku
 * @property-read \App\Models\User $user
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\GiftPackageSet newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\GiftPackageSet newQuery()
 * @method static \Illuminate\Database\Query\Builder|\App\Models\GiftPackageSet onlyTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\GiftPackageSet query()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\GiftPackageSet whereAmount($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\GiftPackageSet whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\GiftPackageSet whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\GiftPackageSet whereGiftPackageId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\GiftPackageSet whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\GiftPackageSet whereProductId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\GiftPackageSet whereProductSkuId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\GiftPackageSet whereShopId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\GiftPackageSet whereSkuImage($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\GiftPackageSet whereSkuPrice($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\GiftPackageSet whereSkuTitle($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\GiftPackageSet whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\GiftPackageSet whereUserId($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\GiftPackageSet withTrashed()
 * @method static \Illuminate\Database\Query\Builder|\App\Models\GiftPackageSet withoutTrashed()
 * @mixin \Eloquent
 */
class GiftPackageSet extends Model
{
    use SoftDeletes;

    protected $guarded = ['id'];

    protected static function boot()
    {
        parent::boot();
        static::creating(function ($model) {
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

    public function product()
    {
        return $this->belongsTo(Product::class)->withTrashed();
    }

    public function productSku()
    {
        return $this->belongsTo(ProductSku::class)->withTrashed();
    }

    public function package()
    {
        return $this->belongsTo(GiftPackage::class);
    }

}
