<?php

namespace App\Http\Controllers;

use App\Models\Banner;
use App\Transformers\BannerTransformer;
use Dingo\Api\Routing\Helpers;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use League\Fractal\Manager as FractalManager;
use League\Fractal\Resource\Collection;


class BannersController extends Controller
{
    use Helpers;
    public function __construct(FractalManager $fractal) {
        parent::__construct();
        $this->fractal = $fractal;
    }
    public function index(Request $request)
    {
        $shop_id = !empty($request['shop_id']) ? intval($request['shop_id']) : config('app.shop_id');
        $banners = Banner::where('is_blocked', 0)->where('shop_id', $shop_id);
        if($request->position){
            $banners = $banners->where('position', $request->position);
        }
        $banners->orderBy('order', 'desc');
        $banners = $banners->paginate(per_page(6));
        return $this->paginator($banners, new BannerTransformer());
    }

    public function show($id)
    {
        // 尽管我们返回的不是 json，但是 dingo 会自动进行转化
        return Lesson::findOrFail($id);
    }
}