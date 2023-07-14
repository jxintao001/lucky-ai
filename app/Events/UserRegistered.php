<?php

namespace App\Events;

use App\Models\User;
use Illuminate\Broadcasting\Channel;
use Illuminate\Queue\SerializesModels;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;

class UserRegistered
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    protected $user;
    protected $inviter_id;

    public function __construct(User $user, $inviter_id)
    {
        $this->user = $user;
        $this->inviter_id = $inviter_id;
    }

    public function getUser()
    {
        return $this->user;
    }

    public function getInviterId()
    {
        return $this->inviter_id;
    }
}
