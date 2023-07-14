<?php

namespace App\Http\Controllers;

use App\Models\CouponCode;
use App\Models\UserCouponCode;
use App\Services\CouponService;
use App\Transformers\CouponCodeTransformer;
use App\Transformers\UserCouponCodeTransformer;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Dingo\Api\Exception\ResourceException;

class CouponCodesController extends Controller
{
    public function index(Request $request)
    {
        //DB::connection()->enableQueryLog(); // 开启查询日志
        $shop_id = !empty($request['shop_id']) ? intval($request['shop_id']) : config('app.shop_id');
        $coupon_codes = CouponCode::with(['userCouponCodes' => function ($query) {
            $query->where('user_id', auth('api')->id());
            }])
            ->where('enabled', 1)
            ->where('shop_id', $shop_id)
            ->whereIn('use_type', [CouponCode::USE_TYPE_NORMAL, CouponCode::USE_TYPE_ASSIGN_PRODUCT])
            ->orderBy('updated_at','desc')
            ->paginate(per_page());
        //dd($coupon_codes->toArray());
        //print_r(DB::getQueryLog());exit();
        return $this->response()->paginator($coupon_codes, new CouponCodeTransformer());
    }

    public function show($id)
    {
        $coupon_code = CouponCode::findOrFail($id);
        return $this->response()->item($coupon_code, new CouponCodeTransformer());
    }

    public function receive($id, CouponService $couponService)
    {
        $user = Auth::user();
        $coupon = CouponCode::findOrFail($id);
        if ($coupon->use_type !== CouponCode::USE_TYPE_NORMAL){
            throw new ResourceException('操作异常,领取失败');
        }
        // 领取操作
        $user_coupon_code = $couponService->receive($user, $coupon);
        $coupon = $user->couponCodes()->where('user_coupon_codes.id', $user_coupon_code->id)->first();
        if (!$coupon){
            throw new ResourceException('领取失败,请稍后再试');
        }
        return $this->response()->item($coupon, new UserCouponCodeTransformer());
    }

    public function newUserReceive(CouponService $couponService, Request $request)
    {
        $user = Auth::user();
        $shop_id = !empty($request['shop_id']) ? intval($request['shop_id']) : config('app.shop_id');
        // 每次进入小程序会调用该接口，顺便更新账号活跃时间
        $user->update([
            'last_actived_at'        => Carbon::now()
        ]);
        $coupon = CouponCode::where('enabled', 1)
                            ->where('use_type', CouponCode::USE_TYPE_NEW_USER)
                            ->where('shop_id', $shop_id)
                            ->first();
        if (!$coupon){
            throw new ResourceException('暂无新人优惠券');
        }
        if ($coupon->use_type !== CouponCode::USE_TYPE_NEW_USER){
            throw new ResourceException('操作异常,领取失败');
        }
        // 领取操作
        $user_coupon_code = $couponService->receive($user, $coupon);
        $coupon = $user->couponCodes()->where('user_coupon_codes.id', $user_coupon_code->id)->first();
        if (!$coupon){
            throw new ResourceException('领取失败,请稍后再试');
        }
        //return $this->response->array(['message'=>'恭喜获得'.$coupon->value.'元'.$coupon->name,'status_code'=>200]);
        return $this->response()->item($coupon, new UserCouponCodeTransformer());
    }

    public function indexByUserId(Request $request)
    {
        $coupon_codes = CouponCode::where('enabled', 1)->orderBy('updated_at','desc')->paginate(per_page());
        //dd($coupon_codes);
        return $this->response()->paginator($coupon_codes, new CouponCodeTransformer());
    }


}