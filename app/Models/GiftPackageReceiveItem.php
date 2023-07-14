<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;


/**
 * App\Models\GiftPackageReceiveItem
 *
 * @property int $id
 * @property int $user_id 用户ID
 * @property int|null $receive_id 领取ID
 * @property int|null $gift_package_id 礼品包ID
 * @property int|null $product_id 商品ID
 * @property int|null $product_sku_id sku ID
 * @property int|null $receive_count 领取礼品数量
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property string|null $deleted_at
 * @property int $shop_id
 * @property-read \App\Models\GiftPackage $package
 * @property-read \App\Models\Product|null $product
 * @property-read \App\Models\ProductSku|null $productSku
 * @property-read \App\Models\GiftPackageReceive|null $receive
 * @property-read \App\Models\User $user
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\GiftPackageReceiveItem newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\GiftPackageReceiveItem newQuery()
 * @method static \Illuminate\Database\Query\Builder|\App\Models\GiftPackageReceiveItem onlyTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\GiftPackageReceiveItem query()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\GiftPackageReceiveItem whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\GiftPackageReceiveItem whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\GiftPackageReceiveItem whereGiftPackageId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\GiftPackageReceiveItem whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\GiftPackageReceiveItem whereProductId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\GiftPackageReceiveItem whereProductSkuId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\GiftPackageReceiveItem whereReceiveCount($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\GiftPackageReceiveItem whereReceiveId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\GiftPackageReceiveItem whereShopId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\GiftPackageReceiveItem whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\GiftPackageReceiveItem whereUserId($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\GiftPackageReceiveItem withTrashed()
 * @method static \Illuminate\Database\Query\Builder|\App\Models\GiftPackageReceiveItem withoutTrashed()
 * @mixin \Eloquent
 * @property string $sku_title sku 标题
 * @property string $sku_image sku 图片
 * @property float|null $sku_price sku 价格
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\GiftPackageReceiveItem whereSkuImage($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\GiftPackageReceiveItem whereSkuPrice($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\GiftPackageReceiveItem whereSkuTitle($value)
 */
class GiftPackageReceiveItem extends Model
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

    public function package()
    {
        return $this->belongsTo(GiftPackage::class);
    }

    public function product()
    {
        return $this->belongsTo(Product::class)->withTrashed();
    }

    public function productSku()
    {
        return $this->belongsTo(ProductSku::class)->withTrashed();
    }

    public function receive()
    {
        return $this->belongsTo(GiftPackageReceive::class);
    }

}
