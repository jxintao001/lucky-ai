<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class WriteOffLog extends Model
{
    use SoftDeletes;

    protected $guarded = ['id'];

    public function store()
    {
        return $this->belongsTo(SupplierStore::class);
    }

    public function order()
    {
        return $this->belongsTo(Order::class);
    }

}
