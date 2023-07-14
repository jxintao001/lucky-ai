<?php

namespace App\Http\Controllers;

use App\Models\Banner;
use App\Models\Post;
use App\Models\Product;
use App\Models\Season;
use App\Models\Shop;
use App\Models\Tag;
use App\Transformers\BannerTransformer;
use App\Transformers\ProductTransformer;
use App\Transformers\SeasonTransformer;
use Carbon\Carbon;
use Dingo\Api\Routing\Helpers;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use League\Fractal\Manager as FractalManager;
use League\Fractal\Resource\Collection;
use League\Fractal\Resource\Item;

class IndexController extends Controller
{
    use Helpers;

    public function __construct(FractalManager $fractal)
    {
        parent::__construct();
        $this->fractal = $fractal;
    }

    public function index()
    {
        $user = Auth::user();
        if ($user) {
            $user->update([
                'last_actived_at' => Carbon::now()
            ]);
        }
        $ret_data = [];
        $shop_id = intval(request('shop_id', config('app.shop_id')));

        // 首页分类
//        $categories = Category::whereIn('shop_id', [$shop_id, Shop::MASTER_SHOP_ID])->where('is_blocked', 0)->where('parent_id', null)->orderBy('order', 'desc')->get();
//        $ret_data['categories'] = $this->fractal->createData(new Collection($categories, new CategoryTransformer()))->toArray();

        // 时节
        $seasons = Season::whereIn('shop_id', [$shop_id, Shop::MASTER_SHOP_ID])->where('is_blocked', 0)->orderBy('order', 'desc')->get();
        $ret_data['seasons'] = $this->fractal->createData(new Collection($seasons, new SeasonTransformer()))->toArray();

        // 获取banners
        $banner_positions = [
            'index_top'       => 'index_top', // 首页-顶部
            'index_position1' => 'index_position1', // 首页-位置1
            'index_position2' => 'index_position2', // 首页-位置2
            'index_position3' => 'index_position3', // 首页-位置3
            'index_position4' => 'index_position4', // 首页-位置4
//            'index_group'     => 'index_group', // 首页-团购
//            'index_bargain'   => 'index_bargain', // 首页-砍价
        ];
        $index_banners = [];
        foreach ($banner_positions as $k => $position) {
            $position_banners = [];
            $position_banners['position'] = $position;
            $banners = Banner::whereIn('shop_id', [$shop_id, Shop::MASTER_SHOP_ID])->where('is_blocked', 0)->where('position', $position)->orderBy('order', 'desc')->take(6)->get();
            $position_banners['banners'] = $this->fractal->createData(new Collection($banners, new BannerTransformer()))->toArray();
            $index_banners[] = $position_banners;
        }
        $ret_data['banners'] = $index_banners;
        // 根据推荐标签获取商品
        $index_products = [];
        $shop = Shop::find($shop_id);
        $tags = Tag::whereIn('shop_id', [$shop_id, Shop::MASTER_SHOP_ID])->where('recommend', 1)->orderBy('order', 'desc')->take(3)->get();
        foreach ($tags as $k => $tag) {
            $position_products = [];
            $position_products['tag_id'] = $tag->id;
            $position_products['tag_name'] = $tag->name;
            $products = Tag::find($tag->id)
                ->products()
                ->onSale()
                ->whereIn('type', [Product::TYPE_NORMAL, Product::TYPE_COMBINATION])
                ->whereIn('products.shop_id', [$shop_id, Shop::MASTER_SHOP_ID])
                ->whereNotIn('products.id', $shop->banned_products)
                ->with('skus')
                ->orderByDesc('products.order')
                ->orderBy('taggables.updated_at', 'desc')
                ->take(100)
                ->get();
            if ($products) {
                $productTransformer = new ProductTransformer();
                foreach ($products as $pk => $pv) {
                    $product = $this->fractal->createData(new Item($pv, $productTransformer))->toArray();
                    $position_products['products']['data'][$pk] = $product['data'] ?? [];
                    $productSku = $pv['skus'][0] ?? [];
                    $position_products['products']['data'][$pk]['sku_id'] = $productSku['id'] ?? 0;
                    if (!empty($productSku['original_price'])) {
                        $position_products['products']['data'][$pk]['original_price'] = foodStampValue($productSku['original_price']);
                    }
                    if (!empty($productSku['data'][0]['tax_price'])) {
                        $position_products['products']['data'][$pk]['price'] = foodStampValue($productSku['tax_price']);
                    }
                    $position_products['products']['data'][$pk]['limit_buy'] = $productSku['limit_buy'] ?? 0;
                    $position_products['products']['data'][$pk]['limit_num'] = $productSku['limit_num'] ?? 0;
                }
            }
            $index_products[] = $position_products;
        }
        $ret_data['products'] = $index_products;
        // 精选商品-销量最高
        $ret_data['select_products'] = [];
        $hot_select_products = [];
        $new_select_products = [];
        $select_products = Product::query()
            ->where('type', Product::TYPE_SELECT)
            ->onSale()->whereIn('shop_id', [$shop_id, Shop::MASTER_SHOP_ID])
            ->whereNotIn('id', $shop->banned_products)
            ->orderByDesc('order')
            ->orderByDesc('sold_count')
            ->get();
        if ($select_products) {
            $productTransformer = new ProductTransformer();
            $productTransformer->setDefaultIncludes(['skus']);
            $select_products = $this->fractal->createData(new Collection($select_products, $productTransformer))->toArray();
            foreach ($select_products['data'] as $k => $product) {
                $productSku = $product['skus'] ?? [];
                $select_products['data'][$k]['sku_id'] = $productSku['data'][0]['id'] ?? 0;
                $select_products['data'][$k]['original_price'] = $productSku['data'][0]['original_price'] ?? 0;
                $select_products['data'][$k]['price'] = $productSku['data'][0]['tax_price'] ?? 0;
                $select_products['data'][$k]['limit_buy'] = $productSku['data'][0]['limit_buy'] ?? 0;
                $select_products['data'][$k]['limit_num'] = $productSku['data'][0]['limit_num'] ?? 0;
                unset($select_products['data'][$k]['skus']);
            }
            $hot_select_products = $select_products['data'] ?? [];
        }
//        $hot_select_product_id = $hot_select_products[0]['id'] ?? 0;
//        // 精选商品-最新上架
//        $select_products = Product::query()
//            ->where('type', Product::TYPE_SELECT)
//            ->onSale()->whereIn('shop_id', [$shop_id, Shop::MASTER_SHOP_ID])
//            ->whereNotIn('id', array_merge($shop->banned_products, [$hot_select_product_id]))
//            ->orderByDesc('id')
//            ->get();
//        if ($select_products) {
//            $productTransformer = new ProductTransformer();
//            $productTransformer->setDefaultIncludes(['skus']);
//            $select_products = $this->fractal->createData(new Collection($select_products, $productTransformer))->toArray();
//            foreach ($select_products['data'] as $k => $product) {
//                $productSku = $product['skus'] ?? [];
//                $select_products['data'][$k]['sku_id'] = $productSku['data'][0]['id'] ?? 0;
//                $select_products['data'][$k]['original_price'] = $productSku['data'][0]['original_price'] ?? 0;
//                $select_products['data'][$k]['price'] = $productSku['data'][0]['tax_price'] ?? 0;
//                $select_products['data'][$k]['limit_buy'] = $productSku['data'][0]['limit_buy'] ?? 0;
//                $select_products['data'][$k]['limit_num'] = $productSku['data'][0]['limit_num'] ?? 0;
//                unset($select_products['data'][$k]['skus']);
//            }
//            $new_select_products = $select_products['data'] ?? [];
//        }

        $ret_data['select_products']['data'] = array_merge($hot_select_products, $new_select_products);
        // 未读消息
        $messages = [
            'users'         => [],
            'message_count' => 0,
        ];
        $message_users = Post::query()
            ->select(DB::raw('*, max(id) as order_id'))
            ->where('is_blocked', 0)
            ->where('is_open', 1)
            ->where('review', 1)
            ->groupBy('user_id')
            ->orderByDesc('order_id')
            ->take(4)
            ->get();
        if ($message_users) {
            foreach ($message_users as $k => $message_user) {
                $messages['users'][] = [
                    'user_id'            => $message_user->user_id,
                    'user_name'          => $message_user->user->name,
                    'user_avatar'        => !empty($message_user->user->avatar) ? $message_user->user->avatar : '',
                    'user_follow_status' => $message_user->user_follow_status,
                ];
            }

            $last_post_read_at = $user && $user->last_post_read_at ? $user->last_post_read_at : 0;
            $messages['message_count'] = Post::query()
                ->where('is_blocked', 0)
                ->where('is_open', 1)
                ->where('created_at', '>', $last_post_read_at)
                ->count();
        }
        $ret_data['messages'] = $messages;

        return $ret_data;
    }
}
