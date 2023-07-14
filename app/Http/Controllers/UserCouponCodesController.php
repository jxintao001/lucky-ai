<?php

namespace App\Http\Controllers;

use App\Models\CouponCode;
use App\Models\UserCouponCode;
use App\Transformers\CouponCodeTransformer;
use App\Transformers\UserCouponCodeTransformer;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;

class UserCouponCodesController extends Controller
{
    public function index(Request $request)
    {
        $status = strval($request['status']);
        //DB::connection()->enableQueryLog(); // 开启查询日志
        $coupons = Auth::user()->couponCodes();
        if ($status == 'usable'){
            $coupons->where('user_coupon_codes.used',0)->where('coupon_codes.not_after','>=',Carbon::now());
        }elseif ($status == 'used'){
            $coupons->where('user_coupon_codes.used',1);
        }elseif ($status == 'expired'){
            $coupons->where('user_coupon_codes.used',0)->where('coupon_codes.not_after','<',Carbon::now());
        }
        $coupons = $coupons->paginate(per_page());
        //print_r(DB::getQueryLog());exit();
        return $this->response()->paginator($coupons, new UserCouponCodeTransformer());
    }

    public function show($id)
    {
        //DB::connection()->enableQueryLog(); // 开启查询日志
        $user = Auth::user();
        $coupon = $user->couponCodes()->where('user_coupon_codes.id', $id)->first();
        if (!$coupon){
            throw new BadRequestHttpException('优惠券不存在');
        }
        //print_r(DB::getQueryLog());exit();
        return $this->response()->item($coupon, new UserCouponCodeTransformer());
    }

    public function receive($id)
    {
        $user = Auth::user();
        $coupon = CouponCode::findOrFail($id);
        $coupon->checkReceive($user);
        $item = new UserCouponCode(['used' => '0']);
        $item->user()->associate($user);
        $item->couponCode()->associate($id);
        $item->save();
        return $this->response->noContent();
    }



}