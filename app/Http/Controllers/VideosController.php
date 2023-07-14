<?php

namespace App\Http\Controllers;

use App\Models\Video;
use App\Models\Shop;
use App\Transformers\VideoTransformer;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class VideosController extends Controller
{
    public function index(Request $request)
    {
        //DB::connection()->enableQueryLog(); // 开启查询日志
        $shop_id = !empty($request['shop_id']) ? intval($request['shop_id']) : config('app.shop_id');
        $act = Video::where('is_blocked', 0)
                ->whereIn('shop_id', [$shop_id, Shop::MASTER_SHOP_ID])
                ->orderBy('order', 'desc')
                ->paginate(per_page());
        //print_r(DB::getQueryLog());exit();
        //return $this->response()->collection($brands, new BrandTransformer());
        return $this->response()->paginator($act, new VideoTransformer());
    
    }

    public function show($id)
    {
        $act = Video::findOrFail($id);
        return $this->response()->item($act, new VideoTransformer());
    }
}