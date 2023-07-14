<?php

namespace App\Http\Controllers;

use App\Http\Requests\UserRequest;
use App\Models\AdminUsers;
use App\Models\User;
use App\Models\UserFriend;
use App\Transformers\UserFollowTransformer;
use App\Transformers\UserInfoTransformer;
use App\Transformers\UserTransformer;
use Dingo\Api\Exception\ResourceException;
use Dingo\Api\Routing\Helpers;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class UsersController extends Controller
{
    use Helpers;

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return $this->response()->item(auth('api')->user(), new UserTransformer());
    }


    /**
     * Display the specified resource.
     *
     * @param int $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $user_info = User::findOrFail($id);
        $user_info->friend_status = 0;
        if (auth('api')->id()) {
            $user_friend = UserFriend::query()
                ->where('user_id', auth('api')->id())
                ->where('friend_id', $user_info->id)
                ->exists();
            $user_info->friend_status = $user_friend ? 1 : 0;
        }
        return $this->response()->item($user_info, new UserInfoTransformer());
    }


    /**
     * Update the specified resource in storage.
     *
     * @param \Illuminate\Http\Request $request
     * @param int $id
     * @return \Illuminate\Http\Response
     */
    public function update(UserRequest $request)
    {
        //验证表单
        $user = auth('api')->user();
        if ($request->exists('name')) $user->name = $request->input('name');
        if ($request->exists('gender') || $request->input('gender') === '0') $user->gender = intval($request->input('gender'));
        if ($request->exists('real_name')) $user->name = $request->input('real_name');
        if ($request->exists('phone')) $user->phone = $request->input('phone');
        if ($request->exists('avatar')) $user->avatar = $request->input('avatar');
        if ($request->exists('introduction')) $user->introduction = $request->input('introduction');
        if ($request->exists('country')) $user->country = $request->input('country');
        if ($request->exists('province')) $user->province = $request->input('province');
        if ($request->exists('city')) $user->city = $request->input('city');
        if ($request->exists('district')) $user->district = $request->input('district');
        if ($request->exists('birthday')) $user->birthday = $request->input('birthday');
        if (!empty($request->input('inviter_code'))) {
            $inviter = User::where('inviter_code', $request->input('inviter_code'))->first();
            if ($inviter) {
                if ($user->level == 0) {
                    $user->inviter_id = $inviter->id;
                    $user->level = 1;
                }
            }
        }
        $user->is_modified = 1;
        $ret = $user->save();
//        if ($ret){
//            $user = User::findOrFail($user->id);
//        }
        //return $this->response()->item($user, new UserTransformer());

        $user = auth()->user();
        $admin_users = AdminUsers::where('user_id', auth('api')->id())->first();
        if ($admin_users) {
            $user->admin_name = $admin_users->username;
            $user->admin_pass = '123456';
        } else {
            $user->admin_name = '';
            $user->admin_pass = '';
        }
        return $this->response()->item($user, new UserTransformer());


    }


    public function follows(Request $request)
    {
        $users = $request->user()->followUsers()->paginate(per_page());
        return $this->response()->paginator($users, new UserFollowTransformer());
    }

    public function follow($id, Request $request)
    {
        $item = User::findOrFail($id);
        $user = $request->user();
        if ($user->followUsers()->find($item->id)) {
            return $this->response->created();
        }
        $user->followUsers()->attach($item);

        return $this->response->created();

    }

    public function cancelFollow($id, Request $request)
    {
        $item = User::findOrFail($id);
        $user = $request->user();
        $user->followUsers()->detach($item);

        return $this->response->noContent();
    }

    public function bindPhone()
    {
        // 参数验证
        if (!request('js_code') || !request('iv') || !request('encrypted_data')) {
            throw new ResourceException('请求参数错误');
        }
        $mini_program = app('wechat.mini_program.default');
        $session = $mini_program->auth->session(request('js_code'));
        if (empty($session['session_key'])) {
            Log::error('bindPhone_session:' . json_encode($session));
            throw new ResourceException($session['errcode'] . ' ' . $session['errmsg']);
        }
        // 解密用户信息
        try {
            $decryptedData = $mini_program->encryptor->decryptData($session['session_key'], request('iv'), request('encrypted_data'));
        } catch (\Exception $e) {
            Log::error('bindPhone_decryptData' . $e->getMessage());
            throw new ResourceException('encrypted_data 解密失败');
        }
        // 解密数据是否为空
        $phone = $decryptedData['phoneNumber'] ?? '';
        if (!$phone) {
            Log::error('bindPhone_decryptedData:', $decryptedData);
            throw new ResourceException('phoneNumber 获取失败');
        }
        $user = auth('api')->user();
        $user->phone = $phone;
        $user->save();
        return $this->response()->item($user, new UserTransformer());
    }

}
