<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;


class Report extends Model
{

    public function products()
    {
        return $this->morphedByMany(Product::class, 'reportables');
    }

}