<?php

namespace App\Http\Controllers;
//header("Access-Control-Allow-Origin: *");

use App\Models\Category;
use App\Models\FoodStampSet;
use App\Models\Product;
use App\Models\ProductSku;
use App\Models\Shop;
use App\Transformers\FoodStampSetTransformer;
use App\Transformers\ProductSkuTransformer;
use App\Transformers\ProductTransformer;
use Carbon\Carbon;
use Illuminate\Http\Request;
use League\Fractal\Manager;
use League\Fractal\Resource\Collection;

class ProductsController extends Controller
{
    public function __construct(Manager $fractal)
    {
        parent::__construct();
        $this->fractal = $fractal;
    }

    public function index_web(Request $request)
    {
        //DB::connection()->enableQueryLog(); // 开启查询日志
        $category_id = intval($request['category_id']);
        $brand_id = intval($request['brand_id']);
        $tag_id = intval($request['tag_id']);
        $search = strval($request['search']);
        $order = strval($request['order']);
        $min_price = strval($request['min_price']);
        $max_price = strval($request['max_price']);
        $shop_id = !empty($request['shop_id']) ? intval($request['shop_id']) : config('app.shop_id');
        $type = !empty($request['type']) ? strval($request['type']) : 'normal';
        $shop = Shop::find($shop_id);
        $products = Product::onSale()
            ->whereIn('shop_id', [$shop_id, Shop::MASTER_SHOP_ID])
            ->whereNotIn('id', $shop->banned_products);
        if ($type) {
            if ($type == Product::TYPE_NORMAL) {
                $products->whereIn('type', [$type, Product::TYPE_COMBINATION]);
            } else {
                $products->where('type', $type);
            }
        }
        if ($min_price) {
            $products->where('price', '>=', $min_price);
        }
        if ($max_price) {
            $products->where('price', '<=', $max_price);
        }
        if ($brand_id) {
            $products->where('brand_id', $brand_id);
        }
        if ($category_id) {
            $categories = Category::where('parent_id', $category_id)->get();
            $collection = collect($categories);
            $collection = $collection->pluck(['id']);
            $collection->push($category_id);
            $products = $products->whereIn('category_id', $collection);
        }

        if ($tag_id) {
            $products = $products->whereHas('tags', function ($query) use ($tag_id) {
                $query->where('id', $tag_id);
            });
        }

        if ($search) {
            $products = $products->where('title', 'like', '%' . $search . '%');
        }

        if ($order) {
            // 是否是以 _asc 或者 _desc 结尾
            if (preg_match('/^(.+)_(asc|desc)$/', $order, $m)) {
                // 如果字符串的开头是这 3 个字符串之一，说明是一个合法的排序值
                if (in_array($m[1], ['price', 'sold_count', 'rating'])) {
                    // 调用查询构造器的排序
                    $products->orderBy($m[1], $m[2]);
                }
            }
        }
        $products = $products->paginate(per_page());
        foreach ($products as $pk => $pv) {
            $sku = ProductSku::where('product_id', $pv['id'])->orderBy('id', 'desc')->take(1)->get();
            $productSku = $this->fractal->createData(new Collection($sku, new ProductSkuTransformer()))->toArray();
            $products[$pk]['sku_id'] = $productSku['data'][0]['id'];
            $products[$pk]['original_price'] = $productSku['data'][0]['original_price'];
            $products[$pk]['price'] = $productSku['data'][0]['price'];
            $products[$pk]['limit_buy'] = $productSku['data'][0]['limit_buy'];
            $products[$pk]['limit_num'] = $productSku['data'][0]['limit_num'];
        }
        return $products;
        exit;
//        print_r($products);exit;
        //print_r(DB::getQueryLog());exit();
        return $this->response()->paginator($products, new ProductTransformer());
    }

    public function index(Request $request)
    {
        //DB::connection()->enableQueryLog(); // 开启查询日志
        $language = $request->language ? $request->language : 'zh';
        $category_id = intval($request['category_id']);
        $brand_id = intval($request['brand_id']);
        $act_id = intval($request['act_id']);
        $tag_id = intval($request['tag_id']);
        $season_id = intval($request['season_id']);
        $search = strval($request['search']);
        $order = !empty($request['order']) ? strval($request['order']) : 'order_desc';
        $min_price = strval($request['min_price']);
        $max_price = strval($request['max_price']);
        $shop_id = !empty($request['shop_id']) ? intval($request['shop_id']) : config('app.shop_id');
        $type = !empty($request['type']) ? strval($request['type']) : 'normal';
        $shop = Shop::find($shop_id);
        $products = Product::onSale()
            ->whereIn('shop_id', [$shop_id, Shop::MASTER_SHOP_ID])
            ->whereNotIn('id', $shop->banned_products);
        
        if ($language) {
//            $products->where('language', $language);
        }
        if ($type) {
            if ($type == Product::TYPE_NORMAL) {
                $products->whereIn('type', [$type, Product::TYPE_COMBINATION]);
            } else {
                $products->where('type', $type);
            }
        }
        if ($min_price) {
            $products->where('price', '>=', $min_price);
        }
        if ($max_price) {
            $products->where('price', '<=', $max_price);
        }
        if ($brand_id) {
            //$products->where('brand_id',$brand_id);
            $products = $products->whereHas('brands', function ($query) use ($brand_id) {
                $query->where('id', $brand_id);
            });
        }

        if ($act_id) {
            //$products->where('brand_id',$brand_id);
            $products = $products->whereHas('acts', function ($query) use ($act_id) {
                $query->where('id', $act_id);
            });
        }

        if ($category_id) {
            $categories = Category::where('parent_id', $category_id)->get();
            $collection = collect($categories);
            $collection = $collection->pluck(['id']);
            $collection->push($category_id);
            $products = $products->whereIn('category_id', $collection);
        }

        if ($tag_id) {
            $products = $products->whereHas('tags', function ($query) use ($tag_id) {
                $query->where('id', $tag_id);
            });
        }

        if ($season_id) {
            $products = $products->whereHas('seasons', function ($query) use ($season_id) {
                $query->where('id', $season_id);
            });
        }

        if ($search) {
            $products = $products->where('title', 'like', '%' . $search . '%');
        }

        if ($order) {
            // 是否是以 _asc 或者 _desc 结尾
            if (preg_match('/^(.+)_(asc|desc)$/', $order, $m)) {
                // 如果字符串的开头是这 3 个字符串之一，说明是一个合法的排序值
                if (in_array($m[1], ['price', 'sold_count', 'rating', 'order'])) {
                    // 调用查询构造器的排序
                    $products->orderBy($m[1], $m[2]);
                }
            }
        }
        $products = $products->paginate(per_page());
        //print_r(DB::getQueryLog());exit();
        return $this->response()->paginator($products, new ProductTransformer());
    }

    public function show($id)
    {

        $product = Product::findOrFail($id);
        return $this->response()->item($product, new ProductTransformer());
    }

    public function favor($id, Request $request)
    {
        $product = Product::findOrFail($id);
        $user = $request->user();
        if ($user->favoriteProducts()->find($product->id)) {
            return $this->response->created();
        }

        $user->favoriteProducts()->attach($product);

        return $this->response->created();
    }

    public function disfavor($id, Request $request)
    {
        $product = Product::findOrFail($id);
        $user = $request->user();
        $user->favoriteProducts()->detach($product);

        return $this->response->noContent();
    }

    public function favorites(Request $request)
    {
        $products = $request->user()->favoriteProducts()->paginate(per_page());
        return $this->response()->paginator($products, new ProductTransformer());
    }


    public function similars($id)
    {
        //DB::connection()->enableQueryLog(); // 开启查询日志
        $shop_id = intval(request('shop_id', config('app.shop_id')));
        $product = Product::findOrFail($id);
        $shop = Shop::find($shop_id);
        $products = Product::onSale()->whereIn('shop_id', [$shop_id, Shop::MASTER_SHOP_ID]);
        $products->where('type', $product->type)->whereNotIn('id', $shop->banned_products);
        if ($product->category_id) {
            $category_id = $product->category_id;
            $categories = Category::where('parent_id', $category_id)->get();
            $collection = collect($categories);
            $collection = $collection->pluck(['id']);
            $collection->push($category_id);
            $products = $products->whereIn('category_id', $collection);
        }
        $products->orderBy('sold_count', 'desc');

        $products = $products->paginate(per_page());
        if ($products->total() == 0) {
            $products = Product::onSale()->whereIn('shop_id', [$shop_id, Shop::MASTER_SHOP_ID]);
            $products->where('type', Product::TYPE_NORMAL)->whereNotIn('id', $shop->banned_products);
            $products->orderBy('sold_count', 'desc');
            $products = $products->paginate(per_page());
        }
        //print_r(DB::getQueryLog());exit();
        return $this->response()->paginator($products, new ProductTransformer());
    }

    public function preferences()
    {
        $shop_id = intval(request('shop_id', config('app.shop_id')));
        $shop = Shop::find($shop_id);
        $products = Product::onSale()->whereIn('shop_id', [$shop_id, Shop::MASTER_SHOP_ID]);
        $products->where('type', Product::TYPE_NORMAL)->whereNotIn('id', $shop->banned_products);
        $products->inRandomOrder();
        $products = $products->paginate(per_page());
        return $this->response()->paginator($products, new ProductTransformer());
    }

    public function foodStampSets(Request $request)
    {
        $shop_id = !empty($request['shop_id']) ? intval($request['shop_id']) : config('app.shop_id');
        $food_stamp_sets = FoodStampSet::query()
            ->where('is_blocked', 0)
            ->where('begin_at', '<=', Carbon::now())
            ->where('end_at', '>=', Carbon::now())
            ->orderBy('food_stamp_amount')
            ->orderBy('id')->paginate(per_page());
        return $this->response()->paginator($food_stamp_sets, new FoodStampSetTransformer());
    }

    public function share($id)
    {
        // 生成微信JS-SDK配置所需的信息
        $config = app('wechat.official_account')->jssdk->buildConfig([
            'onMenuShareTimeline',
            'onMenuShareAppMessage',
        ], false, true, false);
        return response($config);
    }

}