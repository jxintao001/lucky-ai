<?php

namespace App\Http\Controllers;

use App\Models\GiftPackageTemplate;
use App\Models\Shop;
use App\Transformers\GiftPackageTemplateTransfomer;
use Dingo\Api\Routing\Helpers;
use Illuminate\Http\Request;
use League\Fractal\Manager as FractalManager;


class GiftPackageTemplatesController extends Controller
{
    use Helpers;

    public function __construct(FractalManager $fractal)
    {
        parent::__construct();
        $this->fractal = $fractal;
    }

    public function index(Request $request)
    {
        $user_id = (int)Auth('api')->id();
        $shop_id = $user_id ? auth('api')->user()->shop_id : 0;
        $query = GiftPackageTemplate::whereIn('shop_id', [Shop::MASTER_SHOP_ID, $shop_id])
            ->whereIn('user_id', [0, $user_id]);
        if ($request->season_id) {
            $query = $query->where('season_id', $request->season_id);
        }
        $query->orderBy('user_id', 'asc')->orderBy('id');
        $templates = $query->paginate(per_page(6));
        return $this->paginator($templates, new GiftPackageTemplateTransfomer());
    }

    public function show($id)
    {

        $template = GiftPackageTemplate::findOrFail($id);
        return $this->response()->item($template, new GiftPackageTemplateTransfomer());
    }
}