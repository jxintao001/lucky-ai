<?php

require_once '../Config/init.php';
Log::LogWirte("=====================微信支付交易服务端====================");
//==================接收用户数据==========================
$txn_amt = isset($_POST["txn_amt"])? trim($_POST["txn_amt"]):0;//交易金额额
$txn_amt *=100;//金额以分为单位（把元转换成分）    

//================报文组装=================================
$DataContentParms =ARRAY();

$DataContentParms["txn_sub_type"] ="04";//交易子类（固定）
$DataContentParms["member_id"] = $GLOBALS["member_id"];//商户号
$DataContentParms["terminal_id"] = $GLOBALS["terminal_id"];//终端号
$DataContentParms["trans_id"] = "PHPID".get_transid().rand4();
$DataContentParms["trans_serial_no"] = "PHPTSN".get_transid().rand4();
$DataContentParms["txn_amt"] = $txn_amt;
$DataContentParms["trade_date"] = return_time();
$DataContentParms["commodity_name"] = "商品名称";
$DataContentParms["commodity_amount"] = "1";//商品数量
$DataContentParms["user_id"] ="12313213213" ;//平台USER_ID(商户传)
$DataContentParms["user_name"] ="用户姓名" ;//平台用户姓名
$DataContentParms["return_url"] = $GLOBALS["return_url"];//异步接收通知地址。

$DataContentParms["additional_info"] = "附加信息";
$DataContentParms["req_reserved"] = "保留" ;

//==================转换数据类型=============================================
if($GLOBALS["data_type"] == "json"){
	$Encrypted_string = str_replace("\\/", "/",json_encode($DataContentParms,TRUE));//转JSON
}else{
	$toxml = new SdkXML();	//实例化XML转换类
	$Encrypted_string = $toxml->toXml($DataContentParms);//转XML
}

Log::LogWirte("序列化结果：".$Encrypted_string);
$BFRsa = new BFRSA($GLOBALS["pfxfilename"], $GLOBALS["cerfilename"], $GLOBALS["private_key_password"]); //实例化加密类。
$Encrypted = $BFRsa->encryptedByPrivateKey($Encrypted_string);	//先BASE64进行编码再RSA加密

$PostParms =ARRAY();
$PostParms["version"]=$GLOBALS["version"];
$PostParms["terminal_id"]=$GLOBALS["terminal_id"];
$PostParms["txn_type"]="20199";//固定值
$PostParms["txn_sub_type"]=$DataContentParms["txn_sub_type"];//固定值
$PostParms["member_id"]=$GLOBALS["member_id"];
$PostParms["data_type"]=$GLOBALS["data_type"];
$PostParms["data_content"]=$Encrypted;
$RetrunStr = HttpClient::Post($PostParms, SelectUrl::ApiUrl());
Log::LogWirte("查询返回密文：".$RetrunStr);

$RetrunStr = $BFRsa->decryptByPublicKey($RetrunStr);//解密返回的报文
Log::LogWirte("异步通知解密原文：".$RetrunStr);
if(!empty($RetrunStr)){//解析
    $ArrayContent=array();
    if($GLOBALS["data_type"] =="xml"){
        $ArrayContent = SdkXML::XTA($RetrunStr);
    }else{
        $ArrayContent = json_decode($RetrunStr,TRUE);
    }
}else{
    throw new Exception("RSA解密结果为空！");
}

if(array_key_exists("resp_code",$ArrayContent)){
    $Rstr="【状态码：".$ArrayContent["resp_code"]."】,【消息：".$ArrayContent["resp_msg"]."】";
    if($ArrayContent["resp_code"] == "0000"){        
       $Rstr .= ",【token_id：".$ArrayContent["token_id"]."】";
    }
    echo $Rstr;
    
}  else {
    throw new Exception($GLOBALS["data_type"]."解析参数[resp_code]不存在");
}

