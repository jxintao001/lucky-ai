<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * App\Models\Banner
 *
 * @property int $id bannerId
 * @property string $title 标题
 * @property string $cover 图片
 * @property string $target
 * @property string $position 显示位置
 * @property string|null $jump_type 跳转目标类型
 * @property string|null $jump_link 跳转链接
 * @property string|null $description
 * @property int $order 排序DESC
 * @property int $status 进度： 1：即将开始；2：进行中；3：已结束；
 * @property string|null $start_at 开始时间
 * @property string|null $stop_at 结束时间
 * @property int $is_blocked 是否屏蔽（1=屏蔽；0=未屏蔽）
 * @property string $language
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property int $shop_id
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Banner newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Banner newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Banner query()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Banner whereCover($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Banner whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Banner whereDescription($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Banner whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Banner whereIsBlocked($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Banner whereJumpLink($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Banner whereJumpType($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Banner whereLanguage($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Banner whereOrder($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Banner wherePosition($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Banner whereShopId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Banner whereStartAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Banner whereStatus($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Banner whereStopAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Banner whereTarget($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Banner whereTitle($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Banner whereUpdatedAt($value)
 * @mixin \Eloquent
 */
class Banner extends Model
{
    //跳转类型
    const JUMP_TYPE_NORMAL_PRODUCT_INFO = 'normal_product_info';
    const JUMP_TYPE_GROUP_PRODUCT_INFO = 'group_product_info';
    const JUMP_TYPE_BARGAIN_PRODUCT_INFO = 'bargain_product_info';
    const JUMP_TYPE_URL = 'url';
    public static $jumpTypeMap = [
        self::JUMP_TYPE_NORMAL_PRODUCT_INFO => '普通商品详情',
        self::JUMP_TYPE_GROUP_PRODUCT_INFO => '团购商品详情',
        self::JUMP_TYPE_BARGAIN_PRODUCT_INFO => '砍价商品详情',
        self::JUMP_TYPE_URL => 'URL',
    ];

    protected $table = 'banners';

    public function position(){
        return $this->belongsTo(BannerPosition::class, 'position', 'code');
    }
}
