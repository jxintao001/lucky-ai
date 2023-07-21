<?php

namespace App\Http\Controllers;

use App\Models\AccountConfig;
use App\Services\RequestService;
use Dingo\Api\Http\Request;
use Illuminate\Support\Facades\Log;

class AuthController extends Controller
{

    public function douyinOauth(Request $request)
    {
        // 获取请求参数
        $code = $request->input('code');
        // 获取access_token
        $url = 'https://open.douyin.com/oauth/access_token';
        $data = [
            'client_key'    => config('douyin.client_key'),
            'client_secret' => config('douyin.client_secret'),
            'code'          => $code,
            'grant_type'    => 'authorization_code',
        ];
        $requestService = new RequestService();
        $tokenResponse = $requestService->httpPost($url, $data);
        $tokenResponse = json_decode($tokenResponse, true);
        // 写入日志
        Log::error('douyinOauthResponse', $tokenResponse);
        // 获取用户信息
        $url = 'https://open.douyin.com/oauth/userinfo';
        $data = [
            'access_token' => $tokenResponse['data']['access_token'],
            'open_id'      => $tokenResponse['data']['open_id'],
        ];
        $userInfoResponse = $requestService->httpPost($url, $data);
        $userInfoResponse = json_decode($userInfoResponse, true);
        // 写入日志
        Log::error('douyinUserInfoResponse', $userInfoResponse);
        // 查询是否有该用户配置，有则更新，无则创建
        $accountConfig = AccountConfig::where('user_id', $tokenResponse['data']['open_id'])->first();
        if ($accountConfig) {
            $accountConfig->nickname = $userInfoResponse['data']['nickname'];
            $accountConfig->avatar = $userInfoResponse['data']['avatar'];
            $accountConfig->access_token = $tokenResponse['data']['access_token'];
            $accountConfig->refresh_token = $tokenResponse['data']['refresh_token'];
            $accountConfig->save();
        } else {
            $accountConfig = new AccountConfig();
            $accountConfig->user_id = $tokenResponse['data']['open_id'];
            $accountConfig->nickname = $userInfoResponse['data']['nickname'];
            $accountConfig->avatar = $userInfoResponse['data']['avatar'];
            $accountConfig->access_token = $tokenResponse['data']['access_token'];
            $accountConfig->refresh_token = $tokenResponse['data']['refresh_token'];
            $accountConfig->openai_key = config('douyin.openai_key');
            $accountConfig->save();
        }
        return $tokenResponse;

    }

}
