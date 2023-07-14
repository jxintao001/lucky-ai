<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Str;


class Post extends Model
{
    use SoftDeletes;

    protected $guarded = ['id'];

    protected $casts = [
        'product_skus' => 'json',
    ];

    protected static function boot()
    {
        parent::boot();

        static::addGlobalScope('myVote', function (Builder $builder) {
            $builder->with('myVote');
        });
        static::addGlobalScope('myFollow', function (Builder $builder) {
            $builder->with('myFollow');
        });

        static::creating(function ($model) {
            // 创建时自动填充店铺id
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

    public function order()
    {
        return $this->belongsTo(Order::class);
    }

    public function comments()
    {
        return $this->hasMany(Comment::class, 'item_id', 'id');
    }

    public function reviewComments()
    {
        return $this->hasMany(Comment::class, 'item_id', 'id')
            ->where('review', 1)
            ->where('is_blocked', 0);
    }

    public function votes()
    {
        return $this->hasMany(UserVote::class, 'item_id', 'id');
    }

    public function myVote()
    {
        $user_id = !empty(auth('api')->id()) ? auth('api')->id() : 0;
        return $this->hasOne(UserVote::class, 'item_id')
            ->where(['user_votes.user_id' => $user_id]);
    }

    public function myFollow()
    {
        $user_id = !empty(auth('api')->id()) ? auth('api')->id() : 0;
        return $this->hasOne(UserFollow::class, 'item_id', 'user_id')
            ->where(['user_follows.user_id' => $user_id]);
    }

    public function getProductSkusAttribute($value)
    {
        $skus = $value ? json_decode($value, true) : [];
        if (!$skus) {
            return '';
        }
        foreach ($skus as $k => $v) {
            if (!empty($v['product_cover']) && !Str::startsWith($v['product_cover'], ['http://', 'https://'])) {
                $skus[$k]['product_cover'] = config('api.img_host') . $v['product_cover'];
            }

            if (!empty($v['product_sku_price'])) {
                $skus[$k]['product_sku_price'] = foodStampValue($v['product_sku_price']);
            }
        }
        return $skus;
    }

    public function getVoteStatusAttribute()
    {
        return $this->myVote !== null ? 1 : 0;
    }

    public function getUserFollowStatusAttribute()
    {
        return $this->myFollow !== null ? 1 : 0;
    }

}
