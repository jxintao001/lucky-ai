<?php
require_once ('../ebusclient/SettleRequestTransfer.php');

//1、生成同步二级商户及账号请求对象
$tRequest = new SettleRequestTransfer();

//2、设置请求值
$tRequest->request["SettleDate"] = ($_POST['SettleDate']);
$tRequest->request["ZIP"] = ($_POST['ZIP']);

//3.传送交易请求
$tResponse = $tRequest->postRequest();

//3.获取请求结果
if ($tResponse->isSuccess()) {
	print ("<br>Success!!!" . "</br>");
	print ("ReturnCode   = [" . $tResponse->getReturnCode() . "]</br>");
	print ("ReturnMsg   = [" . $tResponse->getErrorMessage() . "]</br>");
	print ("TrxType      = [" . $tResponse->GetValue("TrxType") . "]<br/>");
	print ("SettleDate      = [" . $tResponse->GetValue("SettleDate") . "]<br/>");
  print ("ZIPDetailRecords      = [" . $tResponse->GetValue("ZIPDetailRecords") . "]<br/>");
	print ("DetailRecords      = [" . iconv("GB2312","UTF-8",$tResponse->GetValue("DetailRecords")) . "]<br/>");

} else {
	print ("<br>Failed!!!" . "</br>");
	print ("ReturnCode   = [" . $tResponse->getReturnCode() . "]</br>");
	print ("ReturnMsg   = [" . $tResponse->getErrorMessage() . "]</br>");
}
?>