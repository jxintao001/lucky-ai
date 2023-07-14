<?php
/**
 * 宝付快捷支付-DEMO
 * 本实例依赖包在WEB-IF/lib文件夹内，证书在CER文件夹，配制文件在System_Config/app.properties
 * 实例仅供学习《微信支付》接口使用，仅供参考。商户可根据本实例写自已的代码
 * @author：宝付（大圣）
 * @date:20160704
 */
require_once '../Config/init.php';
Log::LogWirte("=====================普通支付交易====================");
//==================接收用户数据==========================
$txn_amt = isset($_POST["txn_amt"])? trim($_POST["txn_amt"]):0;//交易金额额
$txn_amt *=100;//金额以分为单位（把元转换成分）    

//================报文组装=================================
$DataContentParms =ARRAY();

$DataContentParms["txn_sub_type"] =$GLOBALS["txn_sub_type"];//交易子类
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
$DataContentParms["notice_type"] =$GLOBALS["NoticeType"] ;

$DataContentParms["page_url"] = $GLOBALS["page_url"];//页面通知地址
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

$FormString ="正在处理中，请稍候。。。。。。。。。。。。。。"
	."<body onload=\"document.pay.submit()\"><form id=\"pay\" name=\"pay\" action=\"".SelectUrl::Url($GLOBALS["IsTest"])."\" method=\"post\">"
	."<input name=\"version\" type=\"hidden\" id=\"version\" value=\"".$GLOBALS["version"]."\" />"
	."<input name=\"txn_type\" type=\"hidden\" id=\"txn_type\" value=\"".$GLOBALS["txn_type"]."\" />"
	."<input name=\"txn_sub_type\" type=\"hidden\" id=\"txn_sub_type\" value=\"".$GLOBALS["txn_sub_type"]."\" />"
	."<input name=\"terminal_id\" type=\"hidden\" id=\"terminal_id\" value=\"".$GLOBALS["terminal_id"]."\" />"
	."<input name=\"member_id\" type=\"hidden\" id=\"member_id\" value=\"".$GLOBALS["member_id"]."\" />"
	."<input name=\"data_type\" type=\"hidden\" id=\"data_type\" value=\"".$GLOBALS["data_type"]."\" />"
	."<textarea name=\"data_content\" style=\"display:none;\" id=\"data_content\">".$Encrypted."</textarea>"
	."</form></body>";
Log::LogWirte("请求表单：".$FormString);
echo $FormString;
die();