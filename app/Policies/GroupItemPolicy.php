<?php

namespace App\Policies;

use App\Models\GroupItem;
use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class GroupItemPolicy
{
    use HandlesAuthorization;

    public function own(User $user, GroupItem $groupItem)
    {
        return $groupItem->user_id == $user->id;
    }
}
