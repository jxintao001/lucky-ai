<?php
//====================配置商户的宝付接口授权参数============================
$path = public_path('vender/baofu-wechat-pay');

require_once($path."/Function/BFRSA.php");
require_once($path."/Function/SdkXML.php");
require_once($path."/Function/Log.php");
require_once($path."/Function/HttpClient.php");
require_once($path."/Function/SelectUrl.php");
require_once($path."/Function/phpqrcode.php");
require_once($path."/Function/QRCode.php");

Log::LogWirte("=================微信支付=====================");
//====================配置商户的宝付接口授权参数==============
//$baofu_pay = [];
//$baofu_pay['version'] = Config::get('baofu.wechat.version');//版本号
//$baofu_pay['txn_type'] = Config::get('baofu.wechat.txn_type');//支付交易类型
//$baofu_pay['txn_sub_type'] = Config::get('baofu.wechat.txn_sub_type');//支付交易子类
//
//$baofu_pay['Query_txn_type'] = Config::get('baofu.wechat.query_txn_type');//查询交易类型
//$baofu_pay['Query_txn_sub_type'] = Config::get('baofu.wechat.query_txn_sub_type');//查询交易子类
//
//$baofu_pay['member_id'] = Config::get('baofu.wechat.member_id');	//商户号  --请修改为自己的
//$baofu_pay['terminal_id'] = Config::get('baofu.wechat.terminal_id');	//终端号 --请修改为自己的
//$baofu_pay['data_type'] = Config::get('baofu.wechat.data_type');//加密报文的数据类型（xml/json）
//
//$baofu_pay['private_key_password']  = Config::get('baofu.wechat.private_key_password');	//商户私钥证书密码 --请修改为自己的
//$baofu_pay['pfxfilename'] = Config::get('baofu.wechat.cert_pri');  //注意证书路径是否存在  --请修改为自己的
//$baofu_pay['cerfilename'] = Config::get('baofu.wechat.cert_pub');//注意证书路径是否存在   --请修改为自己的
//
//$baofu_pay['IsTest'] = Config::get('baofu.wechat.is_test');//正式（true）/测试（false）
//$baofu_pay['NoticeType'] = Config::get('baofu.wechat.notice_type'); //1-服务器和页面通知,0-仅服务器通知,3-不通知
//$baofu_pay['page_url'] = Config::get('baofu.wechat.page_url');//页面跳转地址
//$baofu_pay['return_url'] = Config::get('baofu.wechat.return_url');//服务器跳转地址

if(!file_exists(Config::get('baofu.wechat.cert_pri')))
{
    die("私钥证书不存在！<br>");
}
if(!file_exists(Config::get('baofu.wechat.cert_pub')))
{
    die("公钥证书不存在！<br>");
}

function get_transid(){//生成时间戳
	return strtotime(date('Y-m-d H:i:s',time()));	
}
function rand4(){//生成四位随机数
	return rand(1000,9999);
}
function return_time(){//生成时间

	return date('YmdHis',time());
	
}