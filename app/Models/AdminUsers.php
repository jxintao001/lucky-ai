<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * App\Models\AdminUsers
 *
 * @property int $id
 * @property int|null $user_id
 * @property string $username
 * @property string $password
 * @property string $name
 * @property string|null $avatar
 * @property string|null $remember_token
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property int $shop_id 店铺id
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\AdminUsers newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\AdminUsers newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\AdminUsers query()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\AdminUsers whereAvatar($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\AdminUsers whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\AdminUsers whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\AdminUsers whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\AdminUsers wherePassword($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\AdminUsers whereRememberToken($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\AdminUsers whereShopId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\AdminUsers whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\AdminUsers whereUserId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\AdminUsers whereUsername($value)
 * @mixin \Eloquent
 * @property int|null $supplier_id 供应商id
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\AdminUsers whereSupplierId($value)
 */
class AdminUsers extends Model
{
    protected $fillable = [
        'username',
        'name',
    ];
}
