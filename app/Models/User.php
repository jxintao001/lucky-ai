<?php

namespace App\Models;

use Dingo\Api\Exception\ResourceException;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Tymon\JWTAuth\Contracts\JWTSubject;
use Illuminate\Support\Str;

/**
 * App\Models\User
 *
 * @property int $id
 * @property string $name
 * @property string|null $username
 * @property string|null $real_name
 * @property string|null $avatar
 * @property int|null $gender
 * @property string|null $phone
 * @property int $level 用户等级
 * @property float $deposit 押金
 * @property int $integral 积分
 * @property string|null $email
 * @property string|null $password
 * @property string|null $wechat_openid
 * @property string|null $wechat_unionid
 * @property string|null $introduction
 * @property string|null $remember_token
 * @property int $email_verified
 * @property int $is_banned
 * @property string|null $register_source
 * @property int|null $last_shop_id 最后登录的shop_id
 * @property string|null $last_actived_at
 * @property int|null $inviter_id 邀请者id
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property int $shop_id
 * @property-read \App\Models\Level $_level
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\UserAddress[] $addresses
 * @property-read int|null $addresses_count
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\ProductSku[] $cartItems
 * @property-read int|null $cart_items_count
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\CouponCode[] $couponCodes
 * @property-read int|null $coupon_codes_count
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\Product[] $favoriteProducts
 * @property-read int|null $favorite_products_count
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\UserIdentity[] $identities
 * @property-read int|null $identities_count
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\InviteItem[] $inviteItems
 * @property-read int|null $invite_items_count
 * @property-read \Illuminate\Notifications\DatabaseNotificationCollection|\Illuminate\Notifications\DatabaseNotification[] $notifications
 * @property-read int|null $notifications_count
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\Order[] $orders
 * @property-read int|null $orders_count
 * @property-read \App\Models\Shop $shop
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\User newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\User newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\User query()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\User whereAvatar($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\User whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\User whereDeposit($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\User whereEmail($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\User whereEmailVerified($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\User whereGender($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\User whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\User whereIntegral($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\User whereIntroduction($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\User whereInviterId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\User whereIsBanned($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\User whereLastActivedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\User whereLastShopId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\User whereLevel($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\User whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\User wherePassword($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\User wherePhone($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\User whereRealName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\User whereRegisterSource($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\User whereRememberToken($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\User whereShopId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\User whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\User whereUsername($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\User whereWechatOpenid($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\User whereWechatUnionid($value)
 * @mixin \Eloquent
 * @property string|null $country
 * @property string|null $city
 * @property string|null $province
 * @property string|null $language
 * @property string|null $web_openid
 * @property string|null $acess_token
 * @property string|null $refresh_token
 * @property string|null $expires_in
 * @property int|null $inviter_num 邀请人数
 * @property string|null $inviter_code 邀请码
 * @property string|null $admin_name
 * @property string|null $remember_web_token
 * @property string|null $remember_wechat_token
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\User whereAcessToken($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\User whereAdminName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\User whereCity($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\User whereCountry($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\User whereExpiresIn($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\User whereInviterCode($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\User whereInviterNum($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\User whereLanguage($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\User whereProvince($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\User whereRefreshToken($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\User whereRememberWebToken($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\User whereRememberWechatToken($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\User whereWebOpenid($value)
 */
class User extends Authenticatable implements JWTSubject
{
    use Notifiable;

    // Rest omitted for brevity
    protected static function boot()
    {
        parent::boot();
        // 监听模型创建事件，在写入数据库之前触发
        static::creating(function ($model) {
            // 如果模型的 no 字段为空
            if (!$model->username) {
                // 调用 findAvailableNo 生成订单流水号
                $model->username = static::getAvailableUsername();
                // 如果生成失败，则终止创建订单
                if (!$model->username) {
                    return false;
                }
            }
            // 如果模型的 master_card_no 字段为空
            if (!$model->master_card_no) {
                $model->master_card_no = static::getAvailableMasterCardNo();
                if (!$model->master_card_no) {
                    return false;
                }
            }
        });

    }

    /**
     * Get the identifier that will be stored in the subject claim of the JWT.
     *
     * @return mixed
     */
    public function getJWTIdentifier()
    {
        return $this->getKey();
    }

    /**
     * Return a key value array, containing any custom claims to be added to the JWT.
     *
     * @return array
     */
    public function getJWTCustomClaims()
    {
        return [];
    }

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name', 'real_name', 'phone', 'level', 'integral', 'web_openid', 'wechat_openid', 'wechat_unionid', 'gender', 'language', 'city', 'province', 'country', 'email', 'introduction', 'avatar', 'last_shop_id', 'remember_token', 'last_actived_at', 'shop_id', 'inviter_id', 'inviter_code'
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'password',
    ];

    public function orders()
    {
        return $this->hasMany(Order::class);
    }

    public static function getAvailableUsername()
    {
        do {
            $username = rand(100000, 999999999);

        } while (self::query()->where('username', $username)->exists());

        return $username;
    }

    public static function getAvailableMasterCardNo()
    {
        $count = User::max('id');
        do {
            $count++;
            $master_card_no = '1' . str_pad($count, 8, '0', STR_PAD_LEFT);
        } while (self::query()->where('master_card_no', $master_card_no)->exists());

        return $master_card_no;
    }

    public function addresses()
    {
        return $this->hasMany(UserAddress::class);
    }

    public function identities()
    {
        return $this->hasMany(UserIdentity::class);
    }

    public function _level()
    {
        return $this->hasOne(Level::class, 'level', 'level');
    }

//    public function cartItems()
//    {
//        return $this->hasMany(CartItem::class);
//    }

    public function favoriteProducts()
    {
        return $this->belongsToMany(Product::class, 'user_favorite_products')
            ->withTimestamps()
            ->orderBy('user_favorite_products.created_at', 'desc');
    }

    public function upvotePosts()
    {
        return $this->belongsToMany(Post::class, 'user_votes', 'user_id', 'item_id')
            ->withTimestamps()
            ->orderBy('user_votes.created_at', 'desc');
    }

    public function followUsers()
    {
        return $this->belongsToMany(User::class, 'user_follows', 'user_id', 'item_id')
            ->withTimestamps()
            ->orderBy('user_follows.created_at', 'desc');
    }

    public function cartItems($banned_products = [])
    {
        return $this->belongsToMany(ProductSku::class, 'cart_items')
            ->withTimestamps()
            ->withPivot('amount')
            ->whereNotIn('product_skus.product_id', $banned_products)
            ->orderBy('cart_items.created_at', 'desc');
    }

    public function couponCodes()
    {
        return $this->belongsToMany(CouponCode::class, 'user_coupon_codes')
            ->withTimestamps()
            ->withPivot('id', 'used', 'used_at')
            ->orderBy('user_coupon_codes.used', 'asc')
            ->orderBy('user_coupon_codes.updated_at', 'desc');
    }

    public function shop()
    {
        return $this->belongsTo(Shop::class);
    }

    public function inviteItems()
    {
        return $this->hasMany(InviteItem::class);
    }

    public function integrals()
    {
        return $this->hasMany(UserIntegral::class)
            ->orderBy('id', 'desc');
    }

    public function decreaseIntegral($integral)
    {
        if ($integral < 0) {
            throw new ResourceException('减积分不可小于0');
        }

        return $this->newQuery()->where('id', $this->id)->where('integral', '>=', $integral)->decrement('integral', $integral);
    }


    public function decreaseFoodStamp($food_stamp)
    {
        if ($food_stamp < 0) {
            throw new ResourceException('减粮票不可小于0');
        }

        return $this->newQuery()->where('id', $this->id)->where('food_stamp', '>=', $food_stamp)->decrement('food_stamp', $food_stamp);
    }

    public function getAvatarAttribute($value)
    {
        if (!$value) {
            return '';
        }
        // 如果 image 字段本身就已经是完整的 url 就直接返回
        if (Str::startsWith($value, ['http://', 'https://'])) {
            return $value;
        }

        return config('api.img_host') . $value;
    }

    public function getOrderCountAttribute()
    {
        if (auth('api')->user()){
            return Order::query()->where('user_id', auth('api')->id())
                ->where('shop_id', auth('api')->user()->shop_id)
                ->whereIn('type', [Order::TYPE_NORMAL, Order::TYPE_SELECT, Order::TYPE_GIFT])
                ->count();
        }
        return 0;
    }

    public function getGiftCountAttribute()
    {
        if (auth('api')->user()){
            return intval(UserGift::where('user_id', auth('api')->id())
                ->where('shop_id', auth('api')->user()->shop_id)
                ->sum('count'));
        }
        return 0;

    }

    public function getGroupCountAttribute()
    {
        return 0;
    }

    public function getBargainCountAttribute()
    {
        return 0;
    }

}