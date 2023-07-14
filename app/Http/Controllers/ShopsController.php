<?php

namespace App\Http\Controllers;

use App\Models\Shop;
use app\models\User;
use App\Transformers\ShopsTransformer;
use Illuminate\Support\Facades\DB;

class ShopsController extends Controller
{

    public function show($id)
    {
        $shop = Shop::findOrFail($id);
        return $this->response()->item($shop, new ShopsTransformer());
    }


    public function history()
    {
        $user = User::find(auth('api')->id());
        if (!$user->wechat_openid) {
            return $this->errorBadRequest();
        }

        $shops = [];
        $items = DB::table('shops')
            ->select('shops.*')
            ->join('users', 'users.shop_id', '=', 'shops.id')
            ->where('shops.is_banned', 0)
            ->where('users.wechat_openid', $user->wechat_openid)
            ->orderBy('users.last_actived_at', 'desc')
            ->limit(100)
            ->get()
            ->toArray();
        if ($items) {
            foreach ($items as $k => $item) {
                $shops[$k]['id'] = $item->id;
                $shops[$k]['name'] = $item->name;
                $shops[$k]['code'] = !empty($item->code) ? $item->code : '';
                $shops[$k]['logo'] = !empty($item->logo) ? config('api.img_host').$item->logo : '';
                $shops[$k]['qr_code'] = !empty($item->qr_code) ? config('api.img_host').$item->qr_code : '';
                $shops[$k]['introduction'] = !empty($item->introduction) ? $item->introduction : '';
            }
        }
        $ret_data['data'] = $shops;
        return $ret_data;
    }

}