<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Str;


class Invoice extends Model
{
    use SoftDeletes;
    
    const TYPE_NORMAL = 'normal';
    const TYPE_SPECIAL = 'special';
    
    const SEND_MODE_PAPER = 'paper';
    const SEND_MODE_EMAIL = 'email';
    
    const STATUS_PENDING = 'pending';
    const STATUS_SUCCESS = 'success';
    const STATUS_CANCEL = 'cancel';
    
    public static $typeMap = [
        self::TYPE_NORMAL => '普票',
        self::TYPE_SPECIAL => '专票',
    ];
    
    public static $sendModeMap = [
        self::SEND_MODE_PAPER => '纸质',
        self::SEND_MODE_EMAIL => '电子',
    ];
    
    public static $statusMap = [
        self::STATUS_PENDING => '待开',
        self::STATUS_SUCCESS => '已开',
        self::STATUS_CANCEL  => '取消',
    ];
    
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

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function order()
    {
        return $this->belongsTo(Order::class);
    }

}
