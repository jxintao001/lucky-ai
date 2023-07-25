<?php

namespace App\Http\Controllers;

use App\Models\AccountConfig;
use App\Models\Comment;
use App\Services\RequestService;
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
            $requestService = new RequestService();
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
            // 判断是否是回复我的，并组合上下文
            $context = [];
            if (!empty($eventContent['reply_to_comment_id'])) {
                $existComment1 = Comment::where('comment_id', $eventContent['reply_to_comment_id'])->first();
                if ($existComment1 && $existComment1->comment_user_id != $accountConfig->user_id) {
                    return 'ok';
                }
                if ($existComment1 && $existComment1->comment_user_id == $accountConfig->user_id) {
                    $context[] = [
                        "role"    => "assistant",
                        "content" => $existComment1->content
                    ];
                }
                if (!empty($existComment1->reply_to_comment_id)) {
                    $existComment2 = Comment::where('comment_id', $existComment1->reply_to_comment_id)->first();
                    if ($existComment2 && $existComment2->comment_user_id != $accountConfig->user_id) {
                        $context[] = [
                            "role"    => "user",
                            "content" => $existComment2->content
                        ];
                    }
                    if ($existComment2 && $existComment2->comment_user_id == $accountConfig->user_id) {
                        $context[] = [
                            "role"    => "assistant",
                            "content" => $existComment2->content
                        ];
                    }
                }
                if (!empty($existComment2->reply_to_comment_id)) {
                    $existComment3 = Comment::where('comment_id', $existComment2->reply_to_comment_id)->first();
                    if ($existComment3 && $existComment3->comment_user_id != $accountConfig->user_id) {
                        $context[] = [
                            "role"    => "user",
                            "content" => $existComment3->content
                        ];
                    }
                    if ($existComment3 && $existComment3->comment_user_id == $accountConfig->user_id) {
                        $context[] = [
                            "role"    => "assistant",
                            "content" => $existComment3->content
                        ];
                    }
                }
                if (!empty($existComment3->reply_to_comment_id)) {
                    $existComment4 = Comment::where('comment_id', $existComment3->reply_to_comment_id)->first();
                    if ($existComment4 && $existComment4->comment_user_id != $accountConfig->user_id) {
                        $context[] = [
                            "role"    => "user",
                            "content" => $existComment4->content
                        ];
                    }
                    if ($existComment4 && $existComment4->comment_user_id == $accountConfig->user_id) {
                        $context[] = [
                            "role"    => "assistant",
                            "content" => $existComment4->content
                        ];
                    }
                }
                if (!empty($existComment4->reply_to_comment_id)) {
                    $existComment5 = Comment::where('comment_id', $existComment4->reply_to_comment_id)->first();
                    if ($existComment5 && $existComment5->comment_user_id != $accountConfig->user_id) {
                        $context[] = [
                            "role"    => "user",
                            "content" => $existComment5->content
                        ];
                    }
                    if ($existComment5 && $existComment5->comment_user_id == $accountConfig->user_id) {
                        $context[] = [
                            "role"    => "assistant",
                            "content" => $existComment5->content
                        ];
                    }
                }
            }
            // 调用chatGpt接口
            try {
                $url = 'https://api.openai-proxy.com/v1/chat/completions';
                $systemRoleContent = !empty($accountConfig->system_prompt) ? $accountConfig->system_prompt : '我是 ' . $accountConfig->nickname . ' 的智能助手，我会帮Ta回复评论。回复内容总长度绝对不能超过100个字符。';
                $userRoleContent = !empty($eventContent['content']) ? $eventContent['content'] : '讲个笑话';
                $userRoleContent = $userRoleContent . '。 回复内容总长度不能超过100个字符。';
                $data = [
                    "model"    => "gpt-3.5-turbo",
                    "messages" => [
                        [
                            "role"    => "system",
                            "content" => $systemRoleContent
                        ]
                    ]
                ];
                if (!empty($context)) {
                    $context = array_reverse($context);
                    $data['messages'] = array_merge($data['messages'], $context);
                }
                $data['messages'][] = [
                    "role"    => "user",
                    "content" => $userRoleContent
                ];
                // 写入日志
                Log::error('chatGptRequest', [$data]);
                $chatGptResponse = $requestService->httpPostOpenAi($url, $data, $accountConfig->openai_key);
                $chatGptResponse = json_decode($chatGptResponse, true);
                // 写入日志
                Log::error('chatGptResponse1', [$chatGptResponse]);
                $chatGptResponseContent = $chatGptResponse['choices'][0]['message']['content'] ?? $accountConfig->default_reply . '！';
                // 回复内容长度不能超过100个字符
                if (mb_strlen($chatGptResponseContent) > 100) {
                    $chatGptResponseContent = $accountConfig->default_reply . '！！';
                }
            } catch (\Exception $exception) {
                // 写入日志
                Log::error('chatGptResponse2', [$exception->getMessage()]);
                $chatGptResponseContent = $accountConfig->default_reply . '！！！';
            }
            // 回复视频评论
            $url = 'https://open.douyin.com/item/comment/reply/?open_id=' . $accountConfig->user_id;
            $data = [
                'access_token' => $accountConfig->access_token,
                'item_id'      => $eventContent['reply_to_item_id'],
                'comment_id'   => $eventContent['comment_id'],
                'content'      => $chatGptResponseContent,
            ];
            $replyCommentResponse = $requestService->httpPost($url, $data);
            $replyCommentResponse = json_decode($replyCommentResponse, true);
            // 写入日志
            Log::error('replyCommentResponse', $replyCommentResponse);
            // 更新评论表回复标记
            $comment->is_replied = 1;
            $comment->save();
        }
        return 'ok';
    }


}
