<?php

namespace App\Http\Controllers;

use App\Models\CouponCode;
use App\Models\UserActivityItems;
use App\Services\CouponService;
use App\Transformers\UserActivityItem;
use Dingo\Api\Exception\ResourceException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class ActivitiesController extends Controller
{

    public function ranking()
    {
        //DB::connection()->enableQueryLog(); // 开启查询日志

        $user_activity_items = DB::table('user_activity_items')
            ->join('users', 'user_activity_items.user_id', '=', 'users.id')
            ->select(DB::raw('ANY_VALUE(user_activity_items.id) id,ANY_VALUE(user_activity_items.type) type, user_activity_items.user_id,users.name,users.avatar,MAX(user_activity_items.click_count) click_count, ANY_VALUE(user_activity_items.score) score'))
            //->select(DB::raw('ANY_VALUE(user_activity_items.id) id,ANY_VALUE(user_activity_items.type) type, user_activity_items.user_id,users.name,users.avatar,MAX(user_activity_items.click_count) click_count, ANY_VALUE(user_activity_items.score) score,ANY_VALUE(user_activity_items.prize_type) prize_type,ANY_VALUE(user_activity_items.prize_id) prize_id,ANY_VALUE(user_activity_items.created_at) created_at,ANY_VALUE(user_activity_items.updated_at) updated_at'))
            ->where('user_activity_items.type', 'game')
            ->where('user_activity_items.shop_id', request('shop_id', config('app.shop_id')))
            ->groupBy('user_activity_items.user_id')
            ->orderBy('click_count', 'DESC')
            ->orderBy('id', 'ASC')
            ->limit(100)
            ->get()
            ->toArray();
        //print_r(DB::getQueryLog());exit();
        $data = [];
        if ($user_activity_items) {
            $data['data'] = $user_activity_items;
        }
        return $data;
//        $user_activity_items = UserActivityItems::with('user')->select(' ANY_VALUE(id), ANY_VALUE(type), user_id, ANY_VALUE(prize_type),ANY_VALUE(prize_id),ANY_VALUE(created_at),ANY_VALUE(updated_at), ANY_VALUE(score), MAX(click_count) click_count')
//            ->where('type', 'game')
//            ->orderBy('click_count', 'desc')
//            ->orderBy('id', 'desc')
//            ->groupBy('user_id')
//            ->paginate(per_page());
        //dd($user_activity_items->toArray());
        //print_r(DB::getQueryLog());exit();
        return $this->response()->collection($user_activity_items, new UserActivityItem());

    }

    public function prizeReceive(Request $request, CouponService $couponService)
    {
        $id = intval($request['id']);
        $score = intval($request['score']);
//        $click_count = intval($request['click_count']);
        if (!$id) {
            throw new ResourceException('操作异常,提交失败');
        }

        if ($score < 1 || $score > 103) {
            throw new ResourceException('操作异常,提交失败');
        }
//        if ($click_count < 1 || $click_count > 1000){
//            throw new ResourceException('操作异常,提交失败');
//        }
        $user = Auth::user();
        $user_activity_item = UserActivityItems::where('user_id', $user->id)->where('id', $id)->first();
        if (!$user_activity_item) {
            throw new ResourceException('操作异常,提交失败');
        }
        $user_activity_item->update(['score' => $score]);
        //$score = $user_activity_item->score;
        //$click_count = $user_activity_item->click_count;
        // 如果已经领取过奖励
        $received = UserActivityItems::where('user_id', $user->id)->whereIn('prize_id', [8, 9])->first();
        if ($received) {
            throw new ResourceException('已领取过优惠券');
        }
        // 写入数据库
        if ($score >= 98) {
            $coupon_id = 8;
        } elseif ($score >= 90) {
            $coupon_id = 9;
        } elseif ($score >= 70) {
            $coupon_id = 5;
        } else {
            throw new ResourceException('达到70分才能获得奖励');
        }

        if ($coupon_id == 8) {
            $coupon = CouponCode::find($coupon_id);
            if (($coupon->total - $coupon->received) <= 0) {
                $coupon = CouponCode::find(9);
            }
            if (($coupon->total - $coupon->received) <= 0) {
                $coupon = CouponCode::find(5);
            }
        } elseif ($coupon_id == 9) {
            $coupon = CouponCode::find($coupon_id);
            if (($coupon->total - $coupon->received) <= 0) {
                $coupon = CouponCode::find(5);
            }
        } elseif ($coupon_id == 5) {
            $coupon = CouponCode::find($coupon_id);
        }

        // 领取操作
        $user_coupon_code = $couponService->receive($user, $coupon);
        if ($user_coupon_code) {
            $user_activity_item->update(['prize_id' => $coupon->id]);
        }
        return $this->response->array(['message' => '恭喜获得' . $coupon->value . '元' . $coupon->name, 'status_code' => 200]);
    }

    public function submit(Request $request, CouponService $couponService)
    {
        //$score = intval($request['score']);
        $click_count = intval($request['click_count']);
//        if ($score < 1 || $score > 1000){
//            throw new ResourceException('操作异常,提交失败');
//        }
        if ($click_count < 1 || $click_count > 200) {
            throw new ResourceException('操作异常,提交失败');
        }
        $user = Auth::user();
        $user_activity_item = new UserActivityItems([
            'user_id' => $user->id,
            'click_count' => $click_count,
            'prize_type' => 'coupon',
        ]);
        $user_activity_item->save();
        return $this->response()->item($user_activity_item, new UserActivityItem());
    }

    public function prizes()
    {
        //DB::connection()->enableQueryLog(); // 开启查询日志
        $coupon_codes = CouponCode::with(['userCouponCodes' => function ($query) {
            $query->where('user_id', auth('api')->id());
        }])
            ->where('enabled', 1)
            ->whereIn('id', [5, 8, 9])
            ->orderBy('value', 'desc')
            ->get()->toArray();
        $data = [];
        foreach ($coupon_codes as $k => $coupon_code) {
            $data[$k]['id'] = $coupon_code['id'];
            $data[$k]['name'] = $coupon_code['name'];
            if ($coupon_code['id'] == 8) {
                $prize_name = '超级大奖';
            } elseif ($coupon_code['id'] == 9) {
                $prize_name = '年味大礼包';
            } elseif ($coupon_code['id'] == 5) {
                $prize_name = '重磅优惠券';
            } else {
                return [];
            }
            $data[$k]['prize_name'] = $prize_name;

            $data[$k]['min_amount'] = $coupon_code['min_amount'];
            $data[$k]['value'] = $coupon_code['value'];
            $data[$k]['total'] = $coupon_code['total'];
            $data[$k]['surplus'] = max(0, $coupon_code['total'] - $coupon_code['received']);
            $data[$k]['receive_status'] = !empty($coupon_code['user_coupon_codes'][0]) ? '1' : '0';
        }
        //dd($coupon_codes->toArray());
        //print_r(DB::getQueryLog());exit();
        return $data;

    }


    public function checkJoin()
    {
        $user = Auth::user();
        $coupon_codes = CouponCode::where('enabled', 1)
            ->whereIn('id', [8, 9])
            ->get()
            ->toArray();
        if (!$coupon_codes) {
            throw new ResourceException('活动商品已下架');
        }
        if (($coupon_codes[0]['total'] - $coupon_codes[0]['received'] < 1) && ($coupon_codes[1]['total'] - $coupon_codes[1]['received'] < 1)) {
            throw new ResourceException('活动商品已领完');
        }

        $received = UserActivityItems::where('user_id', $user->id)->whereIn('prize_id', [8, 9])->first();
        if (!$received) {
            return $this->response->array(['message' => '可以参与游戏', 'status_code' => 200]);
        }
        throw new ResourceException('已参与游戏并领取过奖励');
    }

}