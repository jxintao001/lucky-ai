<?php

namespace App\Http\Controllers;

use App\Models\AccountConfig;
use App\Models\Comment;
use Dingo\Api\Http\Request;
use Illuminate\Support\Facades\Log;

class EventController extends Controller
{

    public function douyinEvent(Request $request)
    {
        // 获取请求参数
        $data = $request->all();
        // 写入日志
        Log::error('douyinEvent', $data);
        if ($data['event'] === 'item_comment_reply') {
            $accountConfig = AccountConfig::where('user_id', $data['to_user_id'])->first();
            if (!$accountConfig) {
                return 'ok';
            }
            $eventContent = json_decode($data['content'], true);
            // 查下评论是否已经回复过
            $existComment = Comment::where('comment_id', $eventContent['comment_id'])->first();
            if ($existComment) {
                return 'ok';
            }
            // 评论写入数据表
            $comment = new Comment();
            $comment->from_user_id = $data['from_user_id'];
            $comment->to_user_id = $data['to_user_id'];
            $comment->comment_id = $eventContent['comment_id'];
            $comment->comment_user_id = $eventContent['comment_user_id'];
            $comment->content = $eventContent['content'];
            $comment->create_time = $eventContent['create_time'];
            $comment->digg_count = $eventContent['digg_count'];
            $comment->reply_comment_total = $eventContent['reply_comment_total'];
            $comment->reply_to_comment_id = $eventContent['reply_to_comment_id'];
            $comment->reply_to_item_id = $eventContent['reply_to_item_id'];
            $comment->at_user_id = $eventContent['at_user_id'];
            $comment->avatar = $eventContent['avatar'];
            $comment->nick_name = $eventContent['nick_name'];
            $comment->event_data = $eventContent;
            $comment->is_replied = 0;
            $comment->save();
            if ($data['from_user_id'] == $data['to_user_id']) {
                return 'ok';
            }
            // 调用chatGpt接口
            try {
                $url = 'https://api.openai-proxy.com/v1/chat/completions';
                $data = [
                    "model"    => "gpt-3.5-turbo",
                    "messages" => [
                        [
                            "role"    => "system",
                            "content" => "你是一个欧美流行音乐视频号的评论区回复助手，你帮我回复用户评论的留言，以第一人称角色，可以在回复里适当的加上表情，根据实际情况也不用每次都加表情，回复内容字数绝对不能大于50个字"
                        ],
                        [
                            "role"    => "user",
                            "content" => $eventContent['content']
                        ]
                    ]
                ];
                $chatGptResponse = $this->httpPostOpenAi($url, $data, $accountConfig->openai_key);
                $chatGptResponse = json_decode($chatGptResponse, true);
                // 写入日志
                Log::error('chatGptResponse', $chatGptResponse);
                $chatGptResponseContent = $chatGptResponse['choices'][0]['message']['content'] ?? '我有点困了😴，等我休息一下再回复你吧!, 北京时间:' . date('Y-m-d H:i:s');
            } catch (\Exception $exception) {
                $chatGptResponseContent = '我有点困了😴，等我休息一下再回复你吧!!, 北京时间:' . date('Y-m-d H:i:s');
            }
            // 回复视频评论
            $url = 'https://open.douyin.com/item/comment/reply/?open_id=' . $accountConfig->user_id;
            $data = [
                'access_token' => $accountConfig->access_token,
                'item_id'      => $eventContent['reply_to_item_id'],
                'comment_id'   => $eventContent['comment_id'],
                'content'      => $chatGptResponseContent,
            ];
            $replyCommentResponse = $this->httpPost($url, $data);
            $replyCommentResponse = json_decode($replyCommentResponse, true);
            // 写入日志
            Log::error('replyCommentResponse', $replyCommentResponse);
            // 更新评论表回复标记
            $comment->is_replied = 1;
            $comment->save();
        }
        return 'ok';
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
