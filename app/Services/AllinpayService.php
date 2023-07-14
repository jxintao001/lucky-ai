<?php

namespace App\Services;

include_once public_path('vender/allinpay') . '/AppConfig.php';
include_once public_path('vender/allinpay') . '/AppUtil.php';

use Allinpay\AppConfig;
use Allinpay\AppUtil;
use App\Models\Order;
use App\Models\User;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Log;
use Symfony\Component\HttpKernel\Exception\HttpException;

class AllinpayService
{
    public function miniapp($pay_data)
    {
        Log::info("=====================Allinpay微信公从号支付交易====================");
        //==================接收用户数据==========================
        $txn_amt = $pay_data['total_fee']; //交易金额额
        //$txn_amt *= 100;//金额以分为单位（把元转换成分）
        //================报文组装=================================
        $params = array();
        $params["cusid"] = AppConfig::CUSID;
        $params["appid"] = AppConfig::APPID;
        $params["version"] = AppConfig::APIVERSION;
        $params["trxamt"] = $txn_amt;
        $params["reqsn"] = $pay_data['out_trade_no']; //订单号,自行生成
        $params["paytype"] = "W06";
        $params["randomstr"] = time();
        $params["body"] = $pay_data['body'];
        $params["remark"] = "";
        $params["acct"] = $pay_data['openid'];
        //$params["limit_pay"] = "no_credit";
        $params["idno"] = "";
        $params["truename"] = "";
        $params["asinfo"] = "";
        $params["notify_url"] = app('Dingo\Api\Routing\UrlGenerator')->version('v1')->route('payment.allinpay.notify');
        $params["sign"] = AppUtil::SignArray($params, AppConfig::APPKEY); //签名
        // 记录支付信息
        $out_trade_no = explode('_', $pay_data['out_trade_no']);
        $no = $out_trade_no[0];
        $order = Order::where('no', $no)->first();
        $order->update([
            'pay_data' => $params
        ]);
        $paramsStr = AppUtil::ToUrlParams($params);
        Log::info("请求序列化结果：" . $paramsStr);
        
        $url = AppConfig::APIURL . "/pay";
        $rsp = $this->request($url, $paramsStr);
        Log::info("请求响应解密原文：" . $rsp);
        $rspArray = json_decode($rsp, true);
        if (!$this->validSign($rspArray)) {
            throw new HttpException("签名验证失败！");
        }
        return json_decode($rspArray['payinfo'], true);


    }

    public function webpay($pay_data)
    {
        Log::info("=====================Allinpay微信公从号支付交易====================");
        //==================接收用户数据==========================
        $txn_amt = $pay_data['total_fee']; //交易金额额
        //$txn_amt *= 100;//金额以分为单位（把元转换成分）
        //================报文组装=================================
        $params = array();
        $params["cusid"] = AppConfig::CUSID;
        $params["appid"] = AppConfig::APPID;
        $params["version"] = AppConfig::APIVERSION;
        $params["trxamt"] = $txn_amt;
        $params["reqsn"] = $pay_data['out_trade_no']; //订单号,自行生成
        $params["paytype"] = "W01";
        $params["randomstr"] = time();
        $params["body"] = $pay_data['body'];
        $params["remark"] = "";
        $params["acct"] = $pay_data['openid'];
        //$params["limit_pay"] = "no_credit";
        $params["idno"] = "";
        $params["truename"] = "";
        $params["asinfo"] = "";
        $params["notify_url"] = app('Dingo\Api\Routing\UrlGenerator')->version('v1')->route('payment.allinpay.notify');
        $params["sign"] = AppUtil::SignArray($params, AppConfig::APPKEY); //签名
        // 记录支付信息
        $out_trade_no = explode('_', $pay_data['out_trade_no']);
        $no = $out_trade_no[0];
        $order = Order::where('no', $no)->first();
        $order->update([
            'pay_data' => $params
        ]);
        $paramsStr = AppUtil::ToUrlParams($params);
        Log::info("请求序列化结果：" . $paramsStr);
        $url = AppConfig::APIURL . "/pay";
        $rsp = $this->request($url, $paramsStr);
        Log::info("请求响应解密原文：" . $rsp);
        $rspArray = json_decode($rsp, true);
        
        if (!$this->validSign($rspArray)) {
            throw new HttpException("签名验证失败！");
        }
        return $rspArray;
    }
    
    
    // 当天交易用撤销
    public function cancel($pay_data)
    {
        Log::info("=====================Allinpay微信公从号取消交易====================");
        //==================接收用户数据==========================
        $txn_amt = $pay_data['total_fee']; //交易金额额
        //$txn_amt *= 100;//金额以分为单位（把元转换成分）
        //================报文组装=================================
        $params = array();
        $params["cusid"] = AppConfig::CUSID;
        $params["appid"] = AppConfig::APPID;
        $params["version"] = AppConfig::APIVERSION;
        $params["trxamt"] = $txn_amt;
        $params["reqsn"] = $pay_data['out_refund_no'];
        $params["oldreqsn"] = $pay_data['out_trade_no'];
        $params["randomstr"] = time();
        $params["sign"] = AppUtil::SignArray($params, AppConfig::APPKEY); //签名
        $paramsStr = AppUtil::ToUrlParams($params);
        Log::info("请求序列化结果：" . $paramsStr);
        $url = AppConfig::APIURL . "/cancel";
        $rsp = $this->request($url, $paramsStr);
        Log::info("请求响应解密原文：" . $rsp);
        $rspArray = json_decode($rsp, true);
        if (!$this->validSign($rspArray)) {
            throw new HttpException("签名验证失败！");
        }
        if ($rspArray['trxstatus'] != '0000') {
            throw new HttpException($rspArray['errmsg']);
        }
        return $rspArray;


    }

    // 当天交易用退款
    public function refund($pay_data)
    {
        Log::info("=====================Allinpay微信公从号退款====================");
        //==================接收用户数据==========================
        $txn_amt = $pay_data['total_fee']; //交易金额额
        //$txn_amt *= 100;//金额以分为单位（把元转换成分）
        //================报文组装=================================
        $params = array();
        $params["cusid"] = AppConfig::CUSID;
        $params["appid"] = AppConfig::APPID;
        $params["version"] = AppConfig::APIVERSION;
        $params["trxamt"] = $txn_amt;
        $params["reqsn"] = $pay_data['out_refund_no'];
        $params["oldreqsn"] = $pay_data['out_trade_no'];
        $params["randomstr"] = time();
        $params["sign"] = AppUtil::SignArray($params, AppConfig::APPKEY); //签名
        $paramsStr = AppUtil::ToUrlParams($params);
        Log::info("请求序列化结果：" . $paramsStr);
        $url = AppConfig::APIURL . "/refund";
        $rsp = $this->request($url, $paramsStr);
        Log::info("请求响应解密原文：" . $rsp);
        $rspArray = json_decode($rsp, true);
        if (!$this->validSign($rspArray)) {
            throw new HttpException("签名验证失败！");
        }
        if ($rspArray['trxstatus'] != '0000') {
            throw new HttpException($rspArray['errmsg']);
        }
        return $rspArray;


    }

    public function verify()
    {

        Log::info("===================接收异步通知========================");
        $params = array();
        foreach ($_POST as $key => $val) {//动态遍历获取所有收到的参数,此步非常关键,因为收银宝以后可能会加字段,动态获取可以兼容由于收银宝加字段而引起的签名异常
            $params[$key] = $val;
        }
        Log::info("异步通知原文：" . json_encode($params));
        if (count($params) < 1) {//如果参数为空,则不进行处理
            echo "error";
            exit();
        }
        if (!AppUtil::ValidSign($params, AppConfig::APPKEY)) {//验签成功
            echo "error";
            exit();
        }

        return $params;

    }


    public function success()
    {
        echo "success";//接收到通知并处理本地数据后返回OK
        Log::info("返回success");
    }


    //发送请求操作仅供参考,不为最佳实践
    function request($url, $params)
    {
        $ch = curl_init();
        $this_header = array("content-type: application/x-www-form-urlencoded;charset=UTF-8");
        curl_setopt($ch, CURLOPT_HTTPHEADER, $this_header);
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_USERAGENT, 'Mozilla/5.0 (compatible; MSIE 5.01; Windows NT 5.0)');
        curl_setopt($ch, CURLOPT_TIMEOUT, 30);

        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $params);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);//如果不加验证,就设false,商户自行处理
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, FALSE);

        $output = curl_exec($ch);
        curl_close($ch);
        return $output;
    }

    // 验签
    function validSign($array)
    {
        if ("SUCCESS" == $array["retcode"]) {
            $signRsp = strtolower($array["sign"]);
            $array["sign"] = "";
            $sign = strtolower(AppUtil::SignArray($array, AppConfig::APPKEY));
            if ($sign == $signRsp) {
                return true;
            } else {
                throw new HttpException("验签失败:" . $signRsp . "--" . $sign);
            }
        } else {
            throw new HttpException($array['retmsg']);
        }

    }

}
