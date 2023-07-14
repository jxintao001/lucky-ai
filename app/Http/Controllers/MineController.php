<?php

namespace App\Http\Controllers;

use App\Models\Course;
use App\Models\Order;
use App\Transformers\CourseTransformer;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Lcobucci\JWT\Parser;
use Tymon\JWTAuth\Facades\JWTAuth;
use Tymon\JWTAuth\JWT;
use Tymon\JWTAuth\Token;
use Lcobucci\JWT\Builder;
use Lcobucci\JWT\Signer\Hmac\Sha256;
use Lcobucci\JWT\Claim\EqualsTo;

class MineController extends Controller
{
    //我的课程
    public function courses(Request $request)
    {
        $user = auth('api')->user();

        $type = $request['type'];

//        $courses = Course::join('order_details', 'courses.id', '=', 'order_details.product_id')->join('orders', 'order_details.order_id', '=', 'orders.id');
//        !$type ?: $courses = $courses->where('type', $type);
//        $courses = $courses->where('orders.user_id', $user->id)->select('courses.*')->paginate(per_page());

        $courses = Course::whereHas('order', function($q) use ($user,$type){
            $q->where('user_id', '=', $user->id)->where('status', 'completed');
            !$type ?: $q->where('type', $type);
        })->paginate(per_page());


        return $this->response()->paginator($courses, new CourseTransformer());

    }

    //学历教育-我的信息
    public function eduInfo(Request $request){
        $user = auth('api')->user();

        $result = self::curl('http://fxschool.feixiong.tv/api/getWebStudent?uid='.$user->id, '', false);
        return json_decode($result);
    }

    //学历教育-我的作业
    public function eduHomework(Request $request){
        $user = auth('api')->user();
        $page = $request->page ? $request->page : 1;
        $result = self::curl('http://fxschool.feixiong.tv/api/getHomework?page='.$page.'&uid='.$user->id, '', false);
        $result = json_decode($result, true);
        unset($result['first_page_url']);
        unset($result['last_page_url']);
        unset($result['next_page_url']);
        unset($result['path']);
        return $result;
    }

    //学历教育-我的作业-结果
    public function eduHomeworkResult(Request $request){
        $user = auth('api')->user();
        $page = $request->page ? $request->page : 1;
        $result = self::curl('http://fxschool.feixiong.tv/api/getHomeworkResult?page='.$page.'&uid='.$user->id, '', false);
        $result = json_decode($result, true);
        unset($result['first_page_url']);
        unset($result['last_page_url']);
        unset($result['next_page_url']);
        unset($result['path']);
        return $result;
    }

    //学历教育-提交作业
    public function eduHomeworkSend(Request $request){
        $user = auth('api')->user();
        $inputs = $request->all();

        $validator = Validator::make($inputs,[
            'file' => 'required',
            'homework_id' => 'required',
        ]);

        if($validator->fails()){
            return $this->response->error($validator->errors(), 400);
        }

        $file_name = $user->id . "_" . $inputs['homework_id'];
        $path = $request->file('file')->store($file_name);
        $result = self::curl('http://fxschool.feixiong.tv/api/addHomework', ['homework_id'=>$inputs['homework_id'], 'file'=>$path, 'uid'=>$user->id], true);
        $result = json_decode($result, true);
        return $this->response->array($result);
        //return $path;
    }

    public function eduHomeworkUpFile(Request $request){
        $inputs = $request->all();
        $token = $inputs['token'];
        $rst = checkToken($token);
        if($rst){
            $parse = (new Parser())->parse($token);
            $claims = $parse->getClaims();
            $user_id = $claims['sub']->getValue();
        }else{
            return $this->response->error('token wrong!', 400);
        }

        $validator = Validator::make($inputs,[
            'file' => 'required',
            'homework_id' => 'required',
        ]);

        if($validator->fails()){
            return $this->response->error($validator->errors(), 400);
        }

        $file_name = $user_id . "_" . $inputs['homework_id'];
        $path = $request->file('file')->store($file_name);
        $result = self::curl('http://fxschool.feixiong.tv/api/addHomework', ['homework_id'=>$inputs['homework_id'], 'file'=>$path, 'uid'=>$user_id], true);
        $result = json_decode($result, true);
        return $this->response->array($result);
    }

    public function eduSchoolMajors(){
        $user = auth('api')->user();

        $result = self::curl('http://fxschool.feixiong.tv/api/getSchoolsWithMajor?is_reverse=1', '', false);
        return json_decode($result);
    }

    /**
     * @param $url 请求网址
     * @param bool $params 请求参数
     * @param int $ispost 请求方式
     * @param int $https https协议
     * @return bool|mixed
     */
    public static function curl($url, $params = false, $ispost = 0, $https = 0)
    {
        $httpInfo = array();
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_HTTP_VERSION, CURL_HTTP_VERSION_1_1);
        curl_setopt($ch, CURLOPT_USERAGENT, 'Mozilla/5.0 (Windows NT 10.0; WOW64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/41.0.2272.118 Safari/537.36');
        curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 30);
        curl_setopt($ch, CURLOPT_TIMEOUT, 30);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        if ($https) {
            curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE); // 对认证证书来源的检查
            curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, FALSE); // 从证书中检查SSL加密算法是否存在
        }
        if ($ispost) {
            curl_setopt($ch, CURLOPT_POST, true);
            curl_setopt($ch, CURLOPT_POSTFIELDS, $params);
            curl_setopt($ch, CURLOPT_URL, $url);
        } else {
            if ($params) {
                if (is_array($params)) {
                    $params = http_build_query($params);
                }
                curl_setopt($ch, CURLOPT_URL, $url . '?' . $params);
            } else {
                curl_setopt($ch, CURLOPT_URL, $url);
            }
        }

        $response = curl_exec($ch);

        if ($response === FALSE) {
            //echo "cURL Error: " . curl_error($ch);
            return false;
        }
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        $httpInfo = array_merge($httpInfo, curl_getinfo($ch));
        curl_close($ch);
        return $response;
    }
}
