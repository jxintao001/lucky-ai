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
        Log::error('douyinEvent', [$data]);
        if ($data['event'] === 'item_comment_reply'){
            $eventContent = $data['content'];
            // 写入日志
            Log::error('douyinEventContent', [$eventContent]);
            // 调用chatGpt接口
            $url = 'https://api.openai-proxy.com/v1/chat/completions';
            $data = [
                "model" => "gpt-3.5-turbo",
                "messages" => [
                    [
                        "role" => "system",
                        "content" => "你是一个欧美流行音乐视频号的评论区回复助手，你帮我回复用户评论的留言，以第一人称角色，可以在回复里适当的加上表情，比如绝对不能大于40个字"
                    ],
                    [
                        "role" => "user",
                        "content" => $eventContent['content']
                    ]
                ]
            ];
            $chatGptResponse = $this->httpPostOpenAi($url, $data);
            $chatGptResponse = json_decode($chatGptResponse, true);
            // 写入日志
            Log::error('chatGptResponse', $chatGptResponse);
            $chatGptResponseContent = $chatGptResponse['choices'][0]['message']['content'] ?? 'GPT响应失败';
            // 回复视频评论
            $url = 'https://open.douyin.com/item/comment/reply/?open_id=_000AdTrRH90eLIzRLjcwvgkMqVANf4Uj6BW';
            $data = [
                'access_token' => 'act.3.0D0PqvjyjAB0UZsZAAJ3iKaTydthhBj6NMaoVYUouXYsumyw9I_1mHexpA5QsHYSB3w-Vga9ufPXy6xBb8N8HRj_W3Qfb-qtOL3qCv7qYlfKKw2mbOvefoaoH0XzL3JLO7-tEBvGgX1H4xHNJC9V51bSAe9flrs1IzpdlTEOQ6x1fWazxx6WW5-q-Yk=',
                'item_id'      => $eventContent['reply_to_item_id'],
                'comment_id'   => $eventContent['comment_id'],
                'content'      => $chatGptResponseContent,
            ];
            $replyCommentResponse = $this->httpPost($url, $data);
            $replyCommentResponse = json_decode($replyCommentResponse, true);
            // 写入日志
            Log::error('replyCommentResponse', $replyCommentResponse);

        }

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
