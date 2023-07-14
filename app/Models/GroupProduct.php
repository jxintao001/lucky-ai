<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * App\Models\GroupProduct
 *
 * @property int $id
 * @property int $product_id
 * @property float $price
 * @property int $target_count
 * @property \Illuminate\Support\Carbon|null $begin_at
 * @property \Illuminate\Support\Carbon|null $end_at
 * @property int $shop_id
 * @property-read \App\Models\Product $product
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\GroupProduct newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\GroupProduct newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\GroupProduct query()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\GroupProduct whereBeginAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\GroupProduct whereEndAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\GroupProduct whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\GroupProduct wherePrice($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\GroupProduct whereProductId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\GroupProduct whereShopId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\GroupProduct whereTargetCount($value)
 * @mixin \Eloquent
 * @property float|null $tax_rate
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\GroupProduct whereTaxRate($value)
 */
class GroupProduct extends Model
{
    protected $fillable = ['price', 'target_count', 'begin_at', 'end_at'];
    // end_at 会自动转为 Carbon 类型
    protected $dates = ['begin_at','end_at'];
    // 不需要 created_at 和 updated_at 字段
    public $timestamps = false;

    public function product()
    {
        return $this->belongsTo(Product::class);
    }

}
