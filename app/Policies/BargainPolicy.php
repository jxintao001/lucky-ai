<?php

namespace App\Policies;

use App\Models\Bargain;
use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class BargainPolicy
{
    use HandlesAuthorization;

    public function own(User $user, Bargain $bargain)
    {
        return $bargain->user_id == $user->id;
    }
}
