<?php

namespace App\Services;

use App\Models\User;
use App\Models\UserFriend;
use App\Models\UserSecondaryCard;
use Illuminate\Http\Request;

class UserFriendService
{
    public function get(User $user, $type)
    {
        $query = UserSecondaryCard::query();
        if ($type == 'secondary') {
            $query->where('master_user_id', $user->id);
        } else {
            $query->where('user_id', $user->id)->orderByDesc('is_default');
        }
        return $query->orderByDesc('id')->paginate(per_page());
    }

    public function store(User $user, User $friend)
    {
        // 开启事务
        return \DB::transaction(function () use ($user, $friend) {
            $user_friend1 = UserFriend::query()
                ->where('user_id', $user->id)
                ->where('friend_id', $friend->id)
                ->first();
            if (!$user_friend1) {
                $user_friend1 = new UserFriend([
                    'user_id'      => $user->id,
                    'friend_id'    => $friend->id,
                    'friend_name'  => '',
                    'friend_phone' => '',
                    'remark'       => '',
                ]);
                $user_friend1->save();
            }

            $user_friend2 = UserFriend::query()
                ->where('user_id', $friend->id)
                ->where('friend_id', $user->id)
                ->first();
            if (!$user_friend2) {
                $user_friend2 = new UserFriend([
                    'user_id'      => $friend->id,
                    'friend_id'    => $user->id,
                    'friend_name'  => '',
                    'friend_phone' => '',
                    'remark'       => '',
                ]);
                $user_friend2->save();
            }
            return $user_friend1;
        });

    }

    public function remark(UserFriend $user_friend, Request $request)
    {
        // 开启事务
        return \DB::transaction(function () use ($user_friend, $request) {
            $user_friend->update($request->only([
                'friend_name',
                'friend_phone',
                'friend_birthday',
                'relationship',
                'remark',
            ]));
            return $user_friend;
        });
    }


    public function destroy(User $user, UserFriend $user_friend)
    {
        // 开启事务
        \DB::transaction(function () use ($user, $user_friend) {
            UserFriend::query()
                ->where('user_id', $user->id)
                ->where('friend_id', $user_friend->friend_id)
                ->delete();
            UserFriend::query()
                ->where('user_id', $user_friend->friend_id)
                ->where('friend_id', $user->id)
                ->delete();
        });

    }

}
