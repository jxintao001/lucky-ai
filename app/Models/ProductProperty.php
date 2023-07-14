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
 * App\Models\ProductProperty
 *
 * @property int $id
 * @property int $product_id
 * @property string $name
 * @property string $value
 * @property int $shop_id
 * @property-read \App\Models\Product $product
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\ProductProperty newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\ProductProperty newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\ProductProperty query()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\ProductProperty whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\ProductProperty whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\ProductProperty whereProductId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\ProductProperty whereShopId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\ProductProperty whereValue($value)
 * @mixin \Eloquent
 */
class ProductProperty extends Model
{
    public function product(){
        return $this->belongsTo(Product::class);
    }
}