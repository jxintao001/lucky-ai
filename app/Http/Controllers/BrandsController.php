<?php

namespace App\Http\Controllers;

use App\Models\Brand;
use App\Models\Shop;
use App\Transformers\BrandTransformer;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class BrandsController extends Controller
{
    public function index(Request $request)
    {
        //DB::connection()->enableQueryLog(); // 开启查询日志
        $initial = trim($request['initial']);
        $shop_id = !empty($request['shop_id']) ? intval($request['shop_id']) : config('app.shop_id');
        if (!empty($initial)){
            $brands = Brand::where('is_blocked', 0)
                ->whereIn('shop_id', [$shop_id, Shop::MASTER_SHOP_ID])
                ->where('initial', $initial)
                ->orderBy('order', 'desc')
                ->paginate(per_page());
        }else{
            $brands = Brand::where('is_blocked', 0)
                ->whereIn('shop_id', [$shop_id, Shop::MASTER_SHOP_ID])
                ->orderBy('order', 'desc')
                ->orderBy('initial', 'asc')
                ->paginate(per_page());
        }
        //print_r(DB::getQueryLog());exit();
        //return $this->response()->collection($brands, new BrandTransformer());
        return $this->response()->paginator($brands, new BrandTransformer());
    
    }

    public function show($id)
    {
        $brand = Brand::findOrFail($id);
        return $this->response()->item($brand, new BrandTransformer());
    }
}