<?php

namespace App\Http\Controllers;

use App\Models\Comment;
use App\Models\Post;
use App\Transformers\CommentTransformer;
use Dingo\Api\Exception\ResourceException;
use Illuminate\Http\Request;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;

class CommentsController extends Controller
{
    public function index(Request $request)
    {
        $item_id = $request->input('item_id');
        if (!$item_id) {
            throw new ResourceException('item_id参数错误');
        }
        $query = Comment::query()
            ->where('item_id', $item_id)
            ->where('is_blocked', 0)
            ->where('review', 1);
        $comments = $query->orderByDesc('id')->paginate(per_page());
        return $this->response()->paginator($comments, new CommentTransformer());

    }

    public function store(Request $request)
    {
        $item_id = $request->input('item_id');
        $item_type = $request->input('item_type', 'post');
        $content = (string)$request->input('content', '');
        if (!$item_id) {
            throw new ResourceException('item_id参数不能为空');
        }
        if ($content === '') {
            throw new ResourceException('评论内容不能为空');
        }
        if (!in_array($item_type, ['post'])) {
            throw new ResourceException('item_type参数不能为空');
        }
        if ($item_type == 'post') {
            $post = Post::query()->find($item_id);
            if (!$post) {
                throw new ResourceException('无效的item_id');
            }
        }
        $comment = new Comment([
            'content'   => $content,
            'user_id'   => auth('api')->id(),
            'item_id'   => $item_id,
            'item_type' => $item_type,
        ]);
        // 写入数据库
        $comment->save();
        if ($item_type == 'post' && !empty($post)) {
            $post->comment_count = $post->comments->count();
            $post->save();
        }
        return $this->response()->item($comment, new CommentTransformer());
    }

    public function destroy($id)
    {
        $comment = Comment::findOrFail($id);
        if ($comment->user_id != auth('api')->id()) {
            throw new AccessDeniedHttpException('只能删除自己发布的评论');
        }
        $comment->delete();
        if ($comment->item_type == 'post') {
            $comment->post->comment_count = $comment->post->comments->count();
            $comment->post->save();
        }
        return $this->response->noContent();
    }

}