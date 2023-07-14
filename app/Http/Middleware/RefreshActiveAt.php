<?php

namespace App\Http\Middleware;

use App\Models\User;
use Carbon\Carbon;
use Closure;
use Illuminate\Support\Facades\Request;
use Tymon\JWTAuth\Http\Middleware\BaseMiddleware;

// 注意，我们要继承的是 jwt 的 BaseMiddleware
class RefreshActiveAt extends BaseMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request $request
     * @param  \Closure $next
     *
     * @throws \Symfony\Component\HttpKernel\Exception\UnauthorizedHttpException
     *
     * @return mixed
     */
    public function handle($request, Closure $next)
    {

        if (in_array(Request::route()->getName(), ['histories.shop', 'wechat.oauth'])) {
            return $next($request);
        }
        // 如果登录，验证用户
        $user_id = auth('api')->id();
        if (!$user_id) {
            return $next($request);
        }
        $user = User::find($user_id);
        if (!$user) {
            return $next($request);
        }
        // 更新用户活跃时间
        $user->update([
            'last_actived_at' => Carbon::now()
        ]);
        return $next($request);
    }
}