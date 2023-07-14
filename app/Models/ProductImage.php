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
 * App\Models\ProductImage
 *
 * @property int $id
 * @property int $product_id id
 * @property string|null $image
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property int $shop_id
 * @property-read \App\Models\Product $product
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\ProductImage newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\ProductImage newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\ProductImage query()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\ProductImage whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\ProductImage whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\ProductImage whereImage($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\ProductImage whereProductId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\ProductImage whereShopId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\ProductImage whereUpdatedAt($value)
 * @mixin \Eloquent
 */
class ProductImage extends Model
{
    public function product(){
        return $this->belongsTo(Product::class);
    }
}