<?php

namespace App\Http\Controllers;


use App\Models\UserFoodStamp;
use App\Models\UserSecondaryCard;
use App\Transformers\UserFoodStampTransformer;
use Carbon\Carbon;
use Dingo\Api\Exception\ResourceException;
use Illuminate\Http\Request;

class UserFoodStampsController extends Controller
{
    public function index(Request $request)
    {
        $type = $request->input('type');
        $card_uuid = $request->input('card_uuid');
        $date_type = $request->input('date_type');
        $query = UserFoodStamp::query()->with('user','friend');
        if ($type) {
            if (!in_array($type, ['get', 'use'])) {
                throw new ResourceException('type 参数错误');
            }
            $query->where('type', $type);
        }
        if ($date_type) {
            if (!in_array($date_type, [1, 3, 6])) {
                throw new ResourceException('date_type 参数错误');
            }
            $query->where('created_at', '>=', Carbon::parse('-' . $date_type . ' months')->toDateTimeString());
        }
        if ($card_uuid) {
            $card = UserSecondaryCard::query()->where('uuid', $card_uuid)->first();
            $query->where('secondary_card_no', $card->secondary_card_no);
        } else {
            $query->where('master_card_no', auth('api')->user()->master_card_no);
        }
        $user_food_stamps = $query->orderByDesc('id')->paginate(per_page());
        return $this->response()->paginator($user_food_stamps, new UserFoodStampTransformer());
    }


}