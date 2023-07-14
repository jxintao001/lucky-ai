<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;


/**
 * App\Models\UserGift
 *
 * @property int $id
 * @property int $user_id 用户ID
 * @property int|null $product_id 商品ID
 * @property int|null $product_sku_id sku ID
 * @property string $sku_title sku 标题
 * @property string $sku_image sku 图片
 * @property float|null $sku_price sku 价格
 * @property int|null $count 礼品数量
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property string|null $deleted_at
 * @property int $shop_id
 * @property-read \App\Models\Order $order
 * @property-read \App\Models\Product|null $product
 * @property-read \App\Models\ProductSku|null $productSku
 * @property-read \App\Models\User $user
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\UserGift newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\UserGift newQuery()
 * @method static \Illuminate\Database\Query\Builder|\App\Models\UserGift onlyTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\UserGift query()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\UserGift whereCount($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\UserGift whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\UserGift whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\UserGift whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\UserGift whereProductId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\UserGift whereProductSkuId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\UserGift whereShopId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\UserGift whereSkuImage($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\UserGift whereSkuPrice($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\UserGift whereSkuTitle($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\UserGift whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\UserGift whereUserId($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\UserGift withTrashed()
 * @method static \Illuminate\Database\Query\Builder|\App\Models\UserGift withoutTrashed()
 * @mixin \Eloquent
 */
class UserGift extends Model
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


}
