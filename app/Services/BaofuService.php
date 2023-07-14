<?php

namespace App\Services;
include_once public_path('vender/baofu-wechat-pay') . '/Config/init.php';
use Illuminate\Support\Facades\Config;
use Symfony\Component\HttpKernel\Exception\HttpException;

class BaofuService
{
    public function miniapp($pay_data)
    {
        \Log::LogWirte("=====================微信公从号支付交易====================");
        //==================接收用户数据==========================
        $txn_amt = $pay_data['total_fee']; //交易金额额
        $txn_amt *= 100;//金额以分为单位（把元转换成分）
        //================报文组装=================================
        $DataContentParms = ARRAY();
        $DataContentParms["txn_sub_type"] = Config::get('baofu.wechat.txn_sub_type');//交易子类（固定）
        $DataContentParms["member_id"] = Config::get('baofu.wechat.member_id'); //商户号
        $DataContentParms["terminal_id"] = Config::get('baofu.wechat.terminal_id'); //终端号
        $DataContentParms["trans_id"] = $pay_data['trade_no'];
        $DataContentParms["trans_serial_no"] = $pay_data['out_trade_no'];
        $DataContentParms["txn_amt"] = $txn_amt;
        $DataContentParms["trade_date"] = return_time();

        $DataContentParms["appid"] = $pay_data['wechat_app_id'];
        $DataContentParms["is_raw"] = 1;
        $DataContentParms["is_minipg"] = 1;

        $DataContentParms["open_id"] = $pay_data['openid'];
        $DataContentParms["commodity_name"] = "魔饼商品";
        $DataContentParms["commodity_amount"] = "1"; //商品数量
        $DataContentParms["user_id"] = $pay_data['user_id']; //平台USER_ID(商户传)
        $DataContentParms["user_name"] = $pay_data['username']; //平台用户姓名
        $DataContentParms["return_url"] = app('Dingo\Api\Routing\UrlGenerator')->version('v1')->route('payment.baofu.notify');//异步接收通知地址。

        $DataContentParms["additional_info"] = "附加信息";
        $DataContentParms["req_reserved"] = "保留" ;
        //==================转换数据类型=============================================
        if(Config::get('baofu.wechat.data_type') == "json"){
            $Encrypted_string = str_replace("\\/", "/",json_encode($DataContentParms,TRUE));//转JSON
        }else{
            $toxml = new \SdkXML();	//实例化XML转换类
            $Encrypted_string = $toxml->toXml($DataContentParms); //转XML
        }
        \Log::LogWirte("序列化结果：".$Encrypted_string);
        $BFRsa = new \BFRSA(Config::get('baofu.wechat.cert_pri'), Config::get('baofu.wechat.cert_pub'), Config::get('baofu.wechat.private_key_password')); //实例化加密类。
        $Encrypted = $BFRsa->encryptedByPrivateKey($Encrypted_string); //先BASE64进行编码再RSA加密

        $PostParms =ARRAY();
        $PostParms["version"] = Config::get('baofu.wechat.version');
        $PostParms["terminal_id"] = Config::get('baofu.wechat.terminal_id');
        $PostParms["txn_type"] = Config::get('baofu.wechat.txn_type');//固定值
        $PostParms["txn_sub_type"] = $DataContentParms["txn_sub_type"]; //固定值
        $PostParms["member_id"] = Config::get('baofu.wechat.member_id');
        $PostParms["data_type"] = Config::get('baofu.wechat.data_type');
        $PostParms["data_content"] = $Encrypted;
        $RetrunStr = \HttpClient::Post($PostParms, \SelectUrl::ApiUrl());
        \Log::LogWirte("查询返回密文：".$RetrunStr);

        $RetrunStr = $BFRsa->decryptByPublicKey($RetrunStr); //解密返回的报文
        \Log::LogWirte("异步通知解密原文：".$RetrunStr);
        if(!empty($RetrunStr)){ //解析
            $ArrayContent=array();
            if(Config::get('baofu.wechat.data_type') =="xml"){
                $ArrayContent = SdkXML::XTA($RetrunStr);
            }else{
                $ArrayContent = json_decode($RetrunStr,TRUE);
            }
        }else{
            throw new HttpException("RSA解密结果为空！");
        }

        if(!array_key_exists("resp_code",$ArrayContent)){
            throw new HttpException(Config::get('baofu.wechat.data_type')."解析参数[resp_code]不存在");
        }

        if($ArrayContent["resp_code"] != "0000"){
            $Rstr = "【状态码：".$ArrayContent["resp_code"]."】,【消息：".$ArrayContent["resp_msg"]."】";
            throw new HttpException($Rstr);
        }

        if ($DataContentParms["is_raw"] == 1 && $DataContentParms["is_minipg"] == 1){
            return json_decode($ArrayContent['pay_info'], true);
        }else{
            return ['token_id'=>$ArrayContent['token_id']];
        }

    }


    public function verify()
    {
        \Log::LogWirte("===================接收异步通知========================");
        $EndataContent =  isset($_REQUEST["data_content"])?$_REQUEST["data_content"]:die("No parameters are received [data_content]");

        \Log::LogWirte("异步通知原文：".$EndataContent);
        $BFRsa = new \BFRSA(Config::get('baofu.wechat.cert_pri'), Config::get('baofu.wechat.cert_pub'), Config::get('baofu.wechat.private_key_password')); //实例化加密类。
        $ReturnDecode = $BFRsa->decryptByPublicKey($EndataContent);//解密返回的报文

        \Log::LogWirte("异步通知解密原文：".$ReturnDecode);
        if(!empty($ReturnDecode)){//解析
            $ArrayContent = array();
            if(Config::get('baofu.wechat.data_type') =="xml"){
                $ArrayContent = \SdkXML::XTA($ReturnDecode);
            }else{
                $ArrayContent = json_decode($ReturnDecode,TRUE);
            }
        }else{
            throw new HttpException("RSA解密结果为空！");
        }
        \Log::LogWirte("异步通知结果resp_code：".$ArrayContent["resp_code"]);
        return $ArrayContent;

    }


    public function success()
    {
        echo "OK";//接收到通知并处理本地数据后返回OK
        \Log::LogWirte("返回OK");
    }



}
