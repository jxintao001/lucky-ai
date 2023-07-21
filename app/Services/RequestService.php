<?php

namespace App\Services;

class RequestService
{

    // 用第三方包Client post请求 Content-Type ="application/json" 设置header access_token
    public function httpPost($url, $data)
    {
        $client = new \GuzzleHttp\Client();
        $response = $client->request('POST', $url, [
            'headers' => [
                'access-token' => $data['access_token'] ?? '',
                'Content-Type' => 'application/json',
            ],
            'json'    => $data,
        ]);


        return $response->getBody();
    }

    public function httpPostOpenAi($url, $data, $key)
    {
        $client = new \GuzzleHttp\Client();
        $response = $client->request('POST', $url, [
            'headers' => [
                'Content-Type'  => 'application/json',
                'Authorization' => 'Bearer ' . $key,
            ],
            'json'    => $data,
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
