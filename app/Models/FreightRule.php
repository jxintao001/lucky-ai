<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * App\Models\FreightRule
 *
 * @property int $id
 * @property string|null $name
 * @property int $warehouse_id 仓库id
 * @property float $free_shipping 包邮包税金额
 * @property float $basic_freight 基础运费
 * @property int $package_limit 单包裹限制件数
 * @property int $category_limit 单包裹限制类目数
 * @property int $product_limit 单包裹限制商品数
 * @property int|null $is_banned
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property int $shop_id
 * @property-read \App\Models\Warehouse $warehouse
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\FreightRule newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\FreightRule newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\FreightRule query()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\FreightRule whereBasicFreight($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\FreightRule whereCategoryLimit($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\FreightRule whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\FreightRule whereFreeShipping($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\FreightRule whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\FreightRule whereIsBanned($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\FreightRule whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\FreightRule wherePackageLimit($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\FreightRule whereProductLimit($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\FreightRule whereShopId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\FreightRule whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\FreightRule whereWarehouseId($value)
 * @mixin \Eloquent
 */
class FreightRule extends Model
{

    public function warehouse()
    {
        return $this->belongsTo(Warehouse::class);
    }

}
