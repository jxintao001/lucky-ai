<?php

namespace App\Services;

use App\Models\User;
use App\Models\UserSecondaryCard;
use Carbon\Carbon;
use Dingo\Api\Exception\ResourceException;

class CardService
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

    public function store(User $user)
    {
        // 开启事务
        $card = \DB::transaction(function () use ($user) {

            if (!$user->master_card_no) {
                throw new ResourceException('用户主卡不存在');
            }

            // 创建一个订单
            $card = new UserSecondaryCard([
                'master_card_no' => $user->master_card_no,
                'master_user_id' => $user->id,
            ]);
            // 写入数据库
            $card->save();
            return $card;
        });

        return $card;
    }

    public function bind($card_uuid, User $user)
    {
        // 开启事务
        $card = \DB::transaction(function () use ($card_uuid, $user) {
            $card = UserSecondaryCard::query()->where('uuid', $card_uuid)->lockForUpdate()->first();
            if (!$card) {
                throw new ResourceException('无效的card_uuid');
            }
            if ($card->master_user_id == $user->id) {
                throw new ResourceException('不能绑定自己的副卡');
            }
            if ($card->user_id || $card->bound_at) {
                throw new ResourceException('不能重复绑定');
            }
            if (UserSecondaryCard::query()
                ->where('uuid','<>', $card_uuid)
                ->where('master_user_id', $card->master_user_id)
                ->where('user_id', $user->id)
                ->exists()){
                throw new ResourceException('已经绑定过该用户的粮票卡');
            }
            $card->user_id = $user->id;
            $card->bound_at = Carbon::now();
            $card->save();
            return $card;
        });

        return $card;
    }

    public function unbind($card_uuid, User $user)
    {
        // 开启事务
        $card = \DB::transaction(function () use ($card_uuid, $user) {
            $card = UserSecondaryCard::query()->where('uuid', $card_uuid)->where('user_id', $user->id)->lockForUpdate()->first();
            if (!$card) {
                throw new ResourceException('无效的card_uuid');
            }

            $card->user_id = 0;
            $card->bound_at = null;
            $card->is_default = 0;
            $card->save();
            return $card;
        });

        return $card;
    }

    public function default($card_uuid, User $user)
    {
        // 开启事务
        $card = \DB::transaction(function () use ($card_uuid, $user) {
            $card = UserSecondaryCard::query()->where('uuid', $card_uuid)->where('user_id', $user->id)->lockForUpdate()->first();
            if (!$card) {
                throw new ResourceException('无效的card_uuid');
            }

            $card->is_default = 1;
            $card->save();
            UserSecondaryCard::query()
                ->where('user_id', $user->id)
                ->where('id', '!=', $card->id)->update(
                    ['is_default' => 0]
                );
            return $card;
        });

        return $card;
    }

    public function undefault($card_uuid, User $user)
    {
        // 开启事务
        $card = \DB::transaction(function () use ($card_uuid, $user) {
            $card = UserSecondaryCard::query()->where('uuid', $card_uuid)->where('user_id', $user->id)->lockForUpdate()->first();
            if (!$card) {
                throw new ResourceException('无效的card_uuid');
            }
            $card->is_default = 0;
            $card->save();
            return $card;
        });

        return $card;
    }

    public function open($card_uuid, User $user)
    {
        // 开启事务
        $card = \DB::transaction(function () use ($card_uuid, $user) {
            $card = UserSecondaryCard::query()->where('uuid', $card_uuid)->where('master_user_id', $user->id)->lockForUpdate()->first();
            if (!$card) {
                throw new ResourceException('无效的card_uuid');
            }

            $card->is_close = 0;
            $card->save();
            return $card;
        });

        return $card;
    }

    public function close($card_uuid, User $user)
    {
        // 开启事务
        $card = \DB::transaction(function () use ($card_uuid, $user) {
            $card = UserSecondaryCard::query()->where('uuid', $card_uuid)->where('master_user_id', $user->id)->lockForUpdate()->first();
            if (!$card) {
                throw new ResourceException('无效的card_uuid');
            }

            $card->is_close = 1;
            $card->save();
            return $card;
        });

        return $card;
    }

    public function destroy($card_uuid, User $user)
    {
        // 开启事务
        $card = \DB::transaction(function () use ($card_uuid, $user) {
            $card = UserSecondaryCard::query()->where('uuid', $card_uuid)->where('master_user_id', $user->id)->lockForUpdate()->first();
            if (!$card) {
                throw new ResourceException('无效的card_uuid');
            }

            return $card->delete();
        });

        return $card;
    }

}
