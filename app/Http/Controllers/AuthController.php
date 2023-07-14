<?php

namespace App\Http\Controllers;

use Dingo\Api\Http\Request;
use Illuminate\Support\Facades\Log;

class AuthController extends Controller
{
    /**
     * Create a new AuthController instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
        $this->middleware('auth:api', ['except' => ['douyinOauth']]);
    }

    public function douyinOauth(Request $request)
    {
        // 获取请求参数
        $request = $request->all();

        // 写入日志
        Log::error('douyinOauth', $request);

        return $request;

    }

}
