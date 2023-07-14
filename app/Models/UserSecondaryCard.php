<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Ramsey\Uuid\Uuid;

class UserSecondaryCard extends Model
{
    use SoftDeletes;

    protected $guarded = ['id'];

    protected $casts = [

    ];

    protected $dates = [
        'bound_at',
    ];

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($model) {

            if (!$model->secondary_card_no) {
                $model->secondary_card_no = static::getAvailableSecondaryCardNo($model);
                if (!$model->secondary_card_no) {
                    return false;
                }
            }

            if (!$model->uuid) {
                $model->uuid = static::getAvailableUuid();
                if (!$model->uuid) {
                    return false;
                }
            }

            if (!$model->shop_id) {
                if (auth('api')->user()->shop_id) {
                    $model->shop_id = auth('api')->user()->shop_id;
                } else {
                    return false;
                }
            }
        });
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function friend()
    {
        return $this->hasOne(UserFriend::class, 'friend_id', 'user_id')
            ->where('user_friends.user_id', auth('api')->id())->withDefault();
    }

    public function masterFriend()
    {
        return $this->hasOne(UserFriend::class, 'friend_id', 'master_user_id')
            ->where('user_friends.user_id', auth('api')->id())->withDefault();
    }

    public function masterUser()
    {
        return $this->belongsTo(User::class, 'master_user_id', 'id');
    }

    public static function getAvailableUuid()
    {
        do {
            // Uuid类可以用来生成大概率不重复的字符串
            $uuid = Uuid::uuid4()->getHex();
        } while (self::query()->where('uuid', $uuid)->exists());

        return $uuid;
    }

    public static function getAvailableSecondaryCardNo($model)
    {

        if (!$model->master_user_id || !$model->master_card_no) {
            return '';
        }
        $count = self::withTrashed()->where('master_card_no', $model->master_card_no)->count();
        do {
            $count++;
            $card_no = '2' . substr($model->master_card_no, 1) . $count;
        } while (self::query()->where('secondary_card_no', $card_no)->exists());

        return $card_no;
    }


}
