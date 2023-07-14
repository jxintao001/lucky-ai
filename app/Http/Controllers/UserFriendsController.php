<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\UserFriend;
use App\Services\UserFriendService;
use App\Transformers\UserFriendsTransformer;
use Dingo\Api\Exception\ResourceException;
use Illuminate\Http\Request;

class UserFriendsController extends Controller
{
    public function index(Request $request)
    {
        $user_friends = UserFriend::query()
            ->where('user_id', auth('api')->id())
            ->orderBy('created_at', 'desc')
            ->paginate(per_page());
        return $this->response()->paginator($user_friends, new UserFriendsTransformer());
    }


    public function store(Request $request, UserFriendService $service)
    {
        if (!$request->input('friend_id')) {
            throw new ResourceException('friend_id 参数不能为空');
        }
        $user = User::find(auth('api')->id());
        $friend = User::find($request->input('friend_id'));
        if (!$friend) {
            throw new ResourceException('无效的friend_id');
        }
        $user_friend = $service->store($user, $friend);
        return $this->response()->item($user_friend, new UserFriendsTransformer());
    }

    public function remark(Request $request, UserFriendService $service)
    {
        if (!$request->input('friend_id')) {
            throw new ResourceException('friend_id 参数不能为空');
        }
        $user_friend = UserFriend::query()
            ->where('user_id', auth('api')->id())
            ->where('friend_id', $request->input('friend_id'))
            ->first();
        if (!$user_friend) {
            throw new ResourceException('还未添加该好友');
        }
        $user_friend = $service->remark($user_friend, $request);
        return $this->response()->item($user_friend, new UserFriendsTransformer());
    }

    public function destroy($friend_id, UserFriendService $service)
    {
        if (!$friend_id) {
            throw new ResourceException('friend_id 参数不能为空');
        }
        $user_friend = UserFriend::query()
            ->where('user_id', auth('api')->id())
            ->where('friend_id', $friend_id)
            ->first();
        if (!$user_friend) {
            throw new ResourceException('还未添加该好友');
        }
        $user = User::find(auth('api')->id());
        $service->destroy($user, $user_friend);
        return $this->response->noContent();
    }


}
