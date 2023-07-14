<?php

namespace App\Http\Controllers;

use Dingo\Api\Exception\ResourceException;
use Dingo\Api\Routing\Helpers;
use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Routing\Controller as BaseController;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Support\Facades\Auth;

class Controller extends BaseController
{
    use AuthorizesRequests, DispatchesJobs, ValidatesRequests, Helpers;
    public function __construct()
    {
        if (!config('app.shop_id')){
            $shop_id = intval(request('shop_id'));
            if (!$shop_id){
                throw new ResourceException('店铺id不能为空');
            }
            $user = Auth::user();
            if ($user && $user->shop_id != $shop_id){
                throw new ResourceException('店铺id和用户账号信息不匹配');
            }
        }
    }
}
