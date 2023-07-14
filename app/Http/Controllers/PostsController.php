<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\Post;
use App\Models\ProductSku;
use App\Models\User;
use App\Models\UserFollow;
use App\Transformers\PostTransformer;
use Carbon\Carbon;
use Dingo\Api\Exception\ResourceException;
use Illuminate\Http\Request;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;

class PostsController extends Controller
{
    public function index(Request $request)
    {
        if ($request->input('ua')){
            return $_SERVER['HTTP_USER_AGENT'] ?? '';
        }
        $type = $request->input('type', 'hot');
        $order_id = $request->input('order_id');
        $sku_id = $request->input('sku_id');
        if (!in_array($type, ['hot', 'follow', 'comment'])) {
            throw new ResourceException('type参数错误');
        }

        if ($type == 'hot') {
            $query = Post::query()->with('reviewComments')->where('is_blocked', 0)->where('review', 1);
            // 更新用户动态浏览时间
            if (auth('api')->id()) {
                $user = User::query()->find(auth('api')->id());
                $user->last_post_read_at = Carbon::now();
                $user->save();
            }
        } elseif ($type == 'follow') {
            if (!auth('api')->id()) {
                throw new ResourceException('用户未授权登录');
            }
            $query = Post::query()->with('reviewComments')->where('is_blocked', 0)->where('review', 1);
            $user_follows = UserFollow::query()->where('user_id', auth('api')->id())->pluck('item_id')->toArray();
            $query->whereIn('user_id', $user_follows);
            // 更新用户动态浏览时间
            if (auth('api')->id()) {
                $user = User::query()->find(auth('api')->id());
                $user->last_post_read_at = Carbon::now();
                $user->save();
            }
        } elseif ($type == 'comment') {
            $query = Post::query()->with('reviewComments')->where('is_blocked', 0)->where('review', 1);
            if ($order_id) {
                $query->where('order_id', $order_id);
            }
            if ($sku_id) {
                $query->where('product_sku_ids', 'like', '%-' . $sku_id . '-%');
            }
        }

        $posts = $query->orderByDesc('id')->paginate(per_page());
        return $this->response()->paginator($posts, new PostTransformer());

    }

    public function store(Request $request)
    {
        $order_id = $request->input('order_id');
        $sku_id = $request->input('sku_id');
        $rating = $request->input('rating', 5);
        $is_open = $request->input('is_open', 0);
        $content = (string)$request->input('content', '');
        if (!$order_id) {
            throw new ResourceException('order_id参数不能为空');
        }
        if (!$sku_id) {
            throw new ResourceException('sku_id参数不能为空');
        }
        if ($content === '') {
            throw new ResourceException('动态内容不能为空');
        }
        if ($rating < 0 || $rating > 5) {
            throw new ResourceException('评分不能小于0或大于5');
        }
        if ($is_open != 0 && $is_open != 1) {
            throw new ResourceException('公开参数错误');
        }

//        $order = Order::query()->find($order_id);
//        if (!$order) {
//            throw new ResourceException('无效的order_id');
//        }

        $sku = ProductSku::query()->find($sku_id);
        if (!$sku) {
            throw new ResourceException('无效的sku_id');
        }


        $product_ids = '-' . $sku->product_id . '-';
        $product_sku_ids = '-' . $sku->id . '-';
        $product_skus[] = [
            'product_id'        => $sku->product_id,
            'product_type'      => $sku->product->type,
            'product_cover'     => $sku->product->cover,
            'product_sku_id'    => $sku->id,
            'product_sku_price' => $sku->price,
            'product_sku_title' => $sku->title,
        ];
        $post = new Post([
            'title'           => $content,
            'content'         => $content,
            'user_id'         => auth('api')->id(),
            'order_id'        => $order_id,
            'product_ids'     => $product_ids,
            'product_sku_ids' => $product_sku_ids,
            'product_skus'    => $product_skus,
            'rating'          => intval($rating),
            'is_open'         => intval($is_open)
        ]);
        // 写入数据库
        $post->save();
        // 更新订单评论状态
        $order = Order::query()->find($order_id);
        if ($order){
            $order->is_comment = 1;
            $order->save();
        }
        $post = Post::query()->find($post->id);
        return $this->response()->item($post, new PostTransformer());
    }

    public function show($id)
    {
        $post = Post::findOrFail($id);
        return $this->response()->item($post, new PostTransformer());
    }

    public function destroy($id)
    {
        $post = Post::findOrFail($id);
        if ($post->user_id != auth('api')->id()) {
            throw new AccessDeniedHttpException('只能删除自己的动态');
        }
        $post->delete();
        return $this->response->noContent();
    }

    public function upvote($id, Request $request)
    {
        $post = Post::findOrFail($id);
        $user = $request->user();
        if ($user->upvotePosts()->find($post->id)) {
            return $this->response->created();
        }
        $user->upvotePosts()->attach($post);
        $post->vote_count = $post->votes->count();
        $post->save();
        return $this->response->created();

    }

    public function cancelUpvote($id, Request $request)
    {
        $post = Post::findOrFail($id);
        $user = $request->user();
        $user->upvotePosts()->detach($post);
        $post->vote_count = $post->votes->count();
        $post->save();
        return $this->response->noContent();
    }

}