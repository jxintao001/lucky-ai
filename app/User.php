<?php

namespace App;

use Illuminate\Notifications\Notifiable;
use Illuminate\Foundation\Auth\User as Authenticatable;

/**
 * App\User
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
 * @property-read \Illuminate\Notifications\DatabaseNotificationCollection|\Illuminate\Notifications\DatabaseNotification[] $notifications
 * @property-read int|null $notifications_count
 * @method static \Illuminate\Database\Eloquent\Builder|\App\User newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\User newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\User query()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\User whereAvatar($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\User whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\User whereDeposit($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\User whereEmail($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\User whereEmailVerified($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\User whereGender($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\User whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\User whereIntegral($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\User whereIntroduction($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\User whereInviterId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\User whereIsBanned($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\User whereLastActivedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\User whereLastShopId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\User whereLevel($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\User whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\User wherePassword($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\User wherePhone($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\User whereRealName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\User whereRegisterSource($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\User whereRememberToken($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\User whereShopId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\User whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\User whereUsername($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\User whereWechatOpenid($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\User whereWechatUnionid($value)
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
 * @method static \Illuminate\Database\Eloquent\Builder|\App\User whereAcessToken($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\User whereAdminName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\User whereCity($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\User whereCountry($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\User whereExpiresIn($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\User whereInviterCode($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\User whereInviterNum($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\User whereLanguage($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\User whereProvince($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\User whereRefreshToken($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\User whereRememberWebToken($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\User whereRememberWechatToken($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\User whereWebOpenid($value)
 */
class User extends Authenticatable
{
    use Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name', 'email', 'password',
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'password', 'remember_token',
    ];

    // Rest omitted for brevity
}
