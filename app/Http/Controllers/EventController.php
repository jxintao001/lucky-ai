<?php

namespace App\Http\Controllers;

use Dingo\Api\Http\Request;
use Illuminate\Support\Facades\Log;

class EventController extends Controller
{

    public function douyinEvent(Request $request)
    {
        // 获取请求参数
        $data = $request->all();
        // 写入日志
        return json_encode($data['content']);
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

    public function httpPostOpenAi($url, $data)
    {
        $client = new \GuzzleHttp\Client();
        $response = $client->request('POST', $url, [
            'headers' => [
                'Content-Type' => 'application/json',
                'Authorization' => 'Bearer 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',
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
