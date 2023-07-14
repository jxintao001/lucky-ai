<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;


class OfficialAccountAdvertiserLog extends Model
{

    protected $guarded = ['id'];

    protected $casts = [
        'user_data'    => 'json',
        'message_data' => 'json',
    ];

    public function shop()
    {
        return $this->belongsTo(Shop::class);
    }
    
    public function advertiser()
    {
        return $this->belongsTo(Advertiser::class);
    }

}
