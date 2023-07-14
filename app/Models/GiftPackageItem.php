<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;


/**
 * App\Models\GiftPackageItem
 *
 * @property int $id
 * @property int $user_id 用户ID
 * @property int|null $product_id 商品ID
 * @property int|null $product_sku_id sku ID
 * @property int|null $gift_package_id 礼品包ID
 * @property int|null $gift_count 礼品数量
 * @property int|null $receive_count 已领取数量
 * @property float|null $price 价格
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property string|null $deleted_at
 * @property int $shop_id
 * @property-read \App\Models\GiftPackage $package
 * @property-read \App\Models\Product|null $product
 * @property-read \App\Models\ProductSku|null $productSku
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\GiftPackageItem newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\GiftPackageItem newQuery()
 * @method static \Illuminate\Database\Query\Builder|\App\Models\GiftPackageItem onlyTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\GiftPackageItem query()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\GiftPackageItem whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\GiftPackageItem whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\GiftPackageItem whereGiftCount($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\GiftPackageItem whereGiftPackageId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\GiftPackageItem whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\GiftPackageItem wherePrice($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\GiftPackageItem whereProductId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\GiftPackageItem whereProductSkuId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\GiftPackageItem whereReceiveCount($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\GiftPackageItem whereShopId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\GiftPackageItem whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\GiftPackageItem whereUserId($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\GiftPackageItem withTrashed()
 * @method static \Illuminate\Database\Query\Builder|\App\Models\GiftPackageItem withoutTrashed()
 * @mixin \Eloquent
 * @property string $sku_title sku 标题
 * @property string $sku_image sku 图片
 * @property float|null $sku_price sku 价格
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\GiftPackageItem whereSkuImage($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\GiftPackageItem whereSkuPrice($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\GiftPackageItem whereSkuTitle($value)
 */
class GiftPackageItem extends Model
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
