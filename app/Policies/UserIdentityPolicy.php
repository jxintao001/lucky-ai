<?php

namespace App\Policies;

use App\Models\User;
use App\Models\UserIdentity;
use Illuminate\Auth\Access\HandlesAuthorization;

class UserIdentityPolicy
{
    use HandlesAuthorization;

    public function own(User $user, UserIdentity $identity)
    {
        return $identity->user_id == $user->id;
    }
}
