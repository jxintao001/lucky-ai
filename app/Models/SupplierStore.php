<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;


class SupplierStore extends Model
{

    protected $guarded = ['id'];

    public static $token_prefix = '_supplier_store_token_';
    public static $token_expiration = 60 * 24 * 90; // 分钟

    public function getProvinceAttribute($value)
    {
        $area = ChinaArea::where('code', $value)->first();
        return $area->name ?? '';
    }

    public function getCityAttribute($value)
    {
        $area = ChinaArea::where('code', $value)->first();
        return $area->name ?? '';
    }

    public function getDistrictAttribute($value)
    {
        $area = ChinaArea::where('code', $value)->first();
        return $area->name ?? '';
    }

    public function getFullAddressAttribute()
    {
        return "{$this->province}{$this->city}{$this->district}{$this->address}";
    }
}
