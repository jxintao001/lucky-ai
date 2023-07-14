<?php
/**
 * Created by PhpStorm.
 * User: jiaoxintao
 * Date: 2018/10/29
 * Time: 3:47 PM
 */

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
/**
 * App\Models\ProductDetailImage
 *
 * @property int $id
 * @property int $product_id id
 * @property string|null $image
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property int $shop_id
 * @property-read \App\Models\Product $product
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\ProductDetailImage newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\ProductDetailImage newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\ProductDetailImage query()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\ProductDetailImage whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\ProductDetailImage whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\ProductDetailImage whereImage($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\ProductDetailImage whereProductId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\ProductDetailImage whereShopId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\ProductDetailImage whereUpdatedAt($value)
 * @mixin \Eloquent
 */
class ProductDetailImage extends Model
{
    public function product(){
        return $this->belongsTo(Product::class);
    }
}