<?php

namespace App\Http\Controllers;
//header("Access-Control-Allow-Origin: *");

use App\Models\News;
use App\Models\Category;
use App\Transformers\NewsTransformer;
use Illuminate\Http\Request;

class NewsController extends Controller
{
    public function index(Request $request)
    {
        $category_id = intval($request['category_id']);
        $language = $request->language ? $request->language : 'zh';
        $category = Category::find($category_id);
        if (!$category || $category['title_en'] == 'ALL') {
            $news = News::where('is_blocked', 0)->where('language', $language)->orderBy('release_at', 'desc')->paginate(per_page());
        } else {
            $categories = Category::where('parent_id', $category_id)->get();
            $collection = collect($categories);
            $collection = $collection->pluck(['id']);
            $collection->push($category_id);
            $news = News::whereIn('category_id', $collection)->where('is_blocked', 0)->where('language', $language)->orderBy('release_at', 'desc')->paginate(per_page());
        }

        return $this->response()->paginator($news, new NewsTransformer());

    }

    public function show($id)
    {
        $news = News::findOrFail($id);

        return $this->response()->item($news, new NewsTransformer());
    }
}