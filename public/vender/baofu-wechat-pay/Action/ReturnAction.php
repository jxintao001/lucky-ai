<?php
require_once '../Config/init.php';

Log::LogWirte("===================接收异步通知========================");
$EndataContent =  isset($_REQUEST["data_content"])?$_REQUEST["data_content"]:die("No parameters are received [data_content]");

Log::LogWirte("异步通知原文：".$EndataContent);
$BFRsa = new BFRSA($GLOBALS["pfxfilename"], $GLOBALS["cerfilename"], $GLOBALS["private_key_password"]); //实例化加密类。
$ReturnDecode = $BFRsa->decryptByPublicKey($EndataContent);//解密返回的报文

Log::LogWirte("异步通知解密原文：".$ReturnDecode);
if(!empty($ReturnDecode)){//解析
    $ArrayContent=array();
    if($GLOBALS["data_type"] =="xml"){
        $ArrayContent = SdkXML::XTA($ReturnDecode);
    }else{
        $ArrayContent = json_decode($ReturnDecode,TRUE);
    }
}
Log::LogWirte("异步通知结果resp_code：".$ArrayContent["resp_code"]);
//重要返回步聚
echo "OK";//接收到通知并处理本地数据后返回OK
Log::LogWirte("返回OK");