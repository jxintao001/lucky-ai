<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * App\Models\CartItem
 *
 * @property int $id
 * @property int $user_id
 * @property int $product_sku_id
 * @property int $amount
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property int $shop_id
 * @property-read \App\Models\ProductSku $productSku
 * @property-read \App\Models\User $user
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\CartItem newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\CartItem newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\CartItem query()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\CartItem whereAmount($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\CartItem whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\CartItem whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\CartItem whereProductSkuId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\CartItem whereShopId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\CartItem whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\CartItem whereUserId($value)
 * @mixin \Eloquent
 */
class CartItem extends Model
{
    protected $fillable = ['amount','shop_id'];
    public $timestamps = true;

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

    public function productSku()
    {
        return $this->belongsTo(ProductSku::class);
    }
}
