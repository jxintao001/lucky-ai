<?php

namespace App\Http\Controllers;

use App\Models\AccountConfig;
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
            'client_key'    => 'awmoa4k58puqncgy',
            'client_secret' => '7d9574dfd2a09ff5e800a49781d84ee5',
            'code'          => $code,
            'grant_type'    => 'authorization_code',
        ];
        $tokenResponse = $this->httpPost($url, $data);
        $tokenResponse = json_decode($tokenResponse, true);
        // 写入日志
        Log::error('douyinOauthResponse', $tokenResponse);
        // 查询是否有该用户配置，有则更新，无则创建
        $accountConfig = AccountConfig::where('user_id', $tokenResponse['data']['open_id'])->first();
        if ($accountConfig) {
            $accountConfig->access_token = $tokenResponse['data']['access_token'];
            $accountConfig->refresh_token = $tokenResponse['data']['refresh_token'];
            $accountConfig->save();
        } else {
            $accountConfig = new AccountConfig();
            $accountConfig->user_id = $tokenResponse['data']['open_id'];
            $accountConfig->access_token = $tokenResponse['data']['access_token'];
            $accountConfig->refresh_token = $tokenResponse['data']['refresh_token'];
            $accountConfig->openai_key = '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';
            $accountConfig->save();
        }
        return $tokenResponse;

    }



    // 用第三方包Client post请求 Content-Type ="application/json" 设置header access_token
    public function httpPost($url, $data)
    {
        $client = new \GuzzleHttp\Client();
        $response = $client->request('POST', $url, [
            'headers' => [
                'access-token' => $data['access_token'] ?? '',
                'Content-Type' => 'application/json',
            ],
            'json' => $data,
        ]);


        return $response->getBody();
    }

    public function httpPostOpenAi($url, $data, $key)
    {
        $client = new \GuzzleHttp\Client();
        $response = $client->request('POST', $url, [
            'headers' => [
                'Content-Type' => 'application/json',
                'Authorization' => 'Bearer '.$key,
            ],
            'json' => $data,
        ]);


        return $response->getBody();
    }

    // 用第三方包Client get请求
    public function httpGet($url, $data)
    {
        $client = new \GuzzleHttp\Client();
        $response = $client->request('GET', $url, [
            'query' => $data,
        ]);
        return $response->getBody();
    }

}
