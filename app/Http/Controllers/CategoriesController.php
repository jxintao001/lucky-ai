<?php

namespace App\Http\Controllers;

use App\Models\Shop;
use App\Transformers\CategoryTransformer;
use App\Models\Category;
use Illuminate\Http\Request;

class CategoriesController extends Controller
{
    public function index(Request $request)
    {
        $category_id = intval($request['category_id']);
        $shop_id = !empty($request['shop_id']) ? intval($request['shop_id']) : config('app.shop_id');
        if (!empty($category_id)){
            $categories = Category::where('is_blocked', 0)
                ->whereIn('shop_id', [$shop_id, Shop::MASTER_SHOP_ID])
                ->where('parent_id', $category_id)
                ->orderBy('order', 'desc')
                ->get();
        }else{
            $categories = Category::where('is_blocked', 0)
                ->whereIn('shop_id', [$shop_id, Shop::MASTER_SHOP_ID])
                ->whereNull('parent_id')
                ->orderBy('order', 'desc')
                ->get();
        }
        return $this->response()->collection($categories, new CategoryTransformer());

    }

    public function show($id)
    {
        $category = Category::findOrFail($id);
        return $this->response()->item($category, new CategoryTransformer());
    }
}