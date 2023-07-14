<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * App\Models\BargainProduct
 *
 * @property int $id
 * @property int $product_id
 * @property float $target_price
 * @property int|null $upper_limit
 * @property int|null $lower_limit
 * @property \Illuminate\Support\Carbon|null $begin_at
 * @property \Illuminate\Support\Carbon|null $end_at
 * @property int $shop_id
 * @property-read mixed $percent
 * @property-read \App\Models\Product $product
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\BargainProduct newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\BargainProduct newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\BargainProduct query()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\BargainProduct whereBeginAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\BargainProduct whereEndAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\BargainProduct whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\BargainProduct whereLowerLimit($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\BargainProduct whereProductId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\BargainProduct whereShopId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\BargainProduct whereTargetPrice($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\BargainProduct whereUpperLimit($value)
 * @mixin \Eloquent
 * @property int|null $sku_id
 * @property float|null $price 砍走含税价
 * @property float|null $tax_rate 税率
 * @property int|null $amount 数量
 * @property float|null $tax 税费
 * @property int|null $is_open 秒杀中是否开启砍价
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\BargainProduct whereAmount($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\BargainProduct whereIsOpen($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\BargainProduct wherePrice($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\BargainProduct whereSkuId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\BargainProduct whereTax($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\BargainProduct whereTaxRate($value)
 */
class BargainProduct extends Model
{

    protected $fillable = ['target_price','upper_limit','lower_limit', 'begin_at', 'end_at'];
    // end_at 会自动转为 Carbon 类型
    protected $dates = ['begin_at','end_at'];
    // 不需要 created_at 和 updated_at 字段
    public $timestamps = false;

    public function product()
    {
        return $this->belongsTo(Product::class);
    }

    // 定义一个名为 percent 的访问器，返回当前众筹进度
    public function getPercentAttribute()
    {
        // 已筹金额除以目标金额
        $value = $this->attributes['total_amount'] / $this->attributes['target_amount'];

        return floatval(number_format($value * 100, 2, '.', ''));
    }
}
