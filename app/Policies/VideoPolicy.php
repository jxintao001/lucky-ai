<?php

namespace App\Policies;

use App\Models\Course;
use App\Models\User;
use App\Models\Video;
use Illuminate\Auth\Access\HandlesAuthorization;

class VideoPolicy
{
    use HandlesAuthorization;

    public function own(User $user, Video $video)
    {

        return Course::where('is_blocked','=',0)
            ->whereHas('order', function($q) use ($user){
                $q->where('user_id', '=', $user->id);
                $q->Where('status','=','completed');
            })->whereHas('videos', function($q) use ($video){
                $q->where('video_id', '=', $video->id);
            })->exists();
    }
}
