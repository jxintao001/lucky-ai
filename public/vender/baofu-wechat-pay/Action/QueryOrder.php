<?php
require_once '../Config/init.php';
Log::LogWirte("=====================订单查询交易====================");
//==================接收用户数据==========================
$orig_trans_id = isset($_POST["orig_trans_id"])? trim($_POST["orig_trans_id"]):die("参数不能为空【orig_trans_id】");//商户订单号

//================报文组装=================================
$DataContentParms =ARRAY();

$DataContentParms["txn_sub_type"] =$GLOBALS["Query_txn_sub_type"];//交易子类
$DataContentParms["member_id"] = $GLOBALS["member_id"];//商户号
$DataContentParms["terminal_id"] = $GLOBALS["terminal_id"];//终端号
$DataContentParms["trans_serial_no"] = "PHPTSN".get_transid().rand4();
$DataContentParms["orig_trans_id"] = $orig_trans_id;
$DataContentParms["trade_date"] = return_time();

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
$PostParms["txn_type"]=$GLOBALS["Query_txn_type"];
$PostParms["txn_sub_type"]=$GLOBALS["Query_txn_sub_type"];
$PostParms["member_id"]=$GLOBALS["member_id"];
$PostParms["data_type"]=$GLOBALS["data_type"];
$PostParms["data_content"]=$Encrypted;


$RetrunStr = HttpClient::Post($PostParms, SelectUrl::Url($GLOBALS["IsTest"],2));
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
        $Rstr .= ",【成功金额：".$ArrayContent["succ_amt"]."(分)】";
    } 
    echo $Rstr;
    
}  else {
    throw new Exception($GLOBALS["data_type"]."解析参数[resp_code]不存在");
}