<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Str;


/**
 * App\Models\GiftPackageTemplate
 *
 * @property int $id
 * @property int|null $season_id 时节ID
 * @property string $title 模版名
 * @property string $description 描述
 * @property string $image 图片
 * @property string $audio 音频
 * @property string $video 视频
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property string|null $deleted_at
 * @property int $shop_id
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\GiftPackageTemplate newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\GiftPackageTemplate newQuery()
 * @method static \Illuminate\Database\Query\Builder|\App\Models\GiftPackageTemplate onlyTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\GiftPackageTemplate query()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\GiftPackageTemplate whereAudio($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\GiftPackageTemplate whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\GiftPackageTemplate whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\GiftPackageTemplate whereDescription($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\GiftPackageTemplate whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\GiftPackageTemplate whereImage($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\GiftPackageTemplate whereSeasonId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\GiftPackageTemplate whereShopId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\GiftPackageTemplate whereTitle($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\GiftPackageTemplate whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\GiftPackageTemplate whereVideo($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\GiftPackageTemplate withTrashed()
 * @method static \Illuminate\Database\Query\Builder|\App\Models\GiftPackageTemplate withoutTrashed()
 * @mixin \Eloquent
 * @property-read \App\Models\Season|null $season
 */
class GiftPackageTemplate extends Model
{
    use SoftDeletes;

    protected $guarded = ['id'];

    protected static function boot()
    {
        parent::boot();
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

    public function season()
    {
        return $this->belongsTo(Season::class);
    }


    public function getImageAttribute($value)
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

    public function getImagePreviewAttribute($value)
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

    public function getImageShareAttribute($value)
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

    public function getAudioAttribute($value)
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

    public function getVideoAttribute($value)
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
}
