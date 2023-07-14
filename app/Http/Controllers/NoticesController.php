<?php

namespace App\Http\Controllers;

use App\Models\Notice;
use App\Models\Shop;
use App\Transformers\NoticeTransformer;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class NoticesController extends Controller
{
    public function index(Request $request)
    {
        //DB::connection()->enableQueryLog(); // 开启查询日志
        $shop_id = !empty($request['shop_id']) ? intval($request['shop_id']) : config('app.shop_id');
        $act = Notice::where('is_blocked', 0)
                ->whereIn('shop_id', [$shop_id, Shop::MASTER_SHOP_ID])
                ->orderBy('order', 'desc')
                ->paginate(per_page());
        //print_r(DB::getQueryLog());exit();
        //return $this->response()->collection($brands, new BrandTransformer());
        return $this->response()->paginator($act, new NoticeTransformer());
    
    }

    public function show($id)
    {
        $act = Notice::findOrFail($id);
        return $this->response()->item($act, new NoticeTransformer());
    }
}