<?php

namespace App\Http\Controllers;

use Dingo\Api\Http\Request;
use Illuminate\Support\Facades\Log;

class AuthController extends Controller
{

    public function douyinOauth(Request $request)
    {
        // 获取请求参数
        $code = $request->input('code');
        // 写入日志
        Log::error('douyinOauthCode', [$code]);
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
        $access_token = $tokenResponse['data']['access_token'];
        $open_id = $tokenResponse['data']['open_id'];

        // 写入日志
        Log::error('douyinOauthResponse', $tokenResponse);

        // 获取用户信息
        $url = 'https://open.douyin.com/oauth/userinfo';
        $data = [
            'access_token' => $access_token,
            'open_id'      => $open_id,
        ];
        $userInfoResponse = $this->httpPost($url, $data);
        $userInfoResponse = json_decode($userInfoResponse, true);
        // 写入日志
        Log::error('douyinUserInfoResponse', $userInfoResponse);

        // get 获取视频列表
        $url = 'https://open.douyin.com/api/douyin/v1/video/video_list/';
        $data = [
            'access_token' => $access_token,
            'open_id'      => $open_id,
            'count'        => 10,
        ];
        $videoListResponse = $this->httpGet($url, $data);
        $videoListResponse = json_decode($videoListResponse, true);

        $firstVideoId = $videoListResponse['data']['list'][0]['item_id'];
        // 获取评论列表
        $url = 'https://open.douyin.com/item/comment/list/';
        $data = [
            'access_token' => $access_token,
            'open_id'      => $open_id,
            'item_id'      => $firstVideoId,
            'cursor'       => 0,
            'count'        => 10,
        ];
        $commentListResponse = $this->httpGet($url, $data);
        $commentListResponse = json_decode($commentListResponse, true);
        // 写入日志
        Log::error('douyinCommentListResponse', $commentListResponse);
        return $commentListResponse;

    }



    // 用第三方包Client post请求 Content-Type ="application/json"

    public function httpPost($url, $data)
    {
        $client = new \GuzzleHttp\Client();
        $response = $client->request('POST', $url, [
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
