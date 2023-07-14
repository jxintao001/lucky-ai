<?php
require_once ('../ebusclient/SubMerAccBalQryRequest.php');

//1、生成同步二级商户及账号请求对象
$tRequest = new SubMerAccBalQryRequest();

//2、设置请求值
$tRequest->request["SubMerId"] = ($_POST['SubMerId']);

//3.传送交易请求
$tResponse = $tRequest->postRequest();

//3.获取请求结果
if ($tResponse->isSuccess()) {
	print ("<br>Success!!!" . "</br>");
	print ("ReturnCode   = [" . $tResponse->getReturnCode() . "]</br>");
	print ("ReturnMsg   = [" . $tResponse->getErrorMessage() . "]</br>");
	print ("MerchantName   = [" . $tResponse->GetValue("MerchantName") . "]<br/>");
	print ("SubMerId = [" . $tResponse->GetValue("SubMerId") . "]<br/>");
	print ("SubMerchantAccNo   = [" . $tResponse->GetValue("SubMerchantAccNo") . "]<br/>");
	print ("Balance   = [" . $tResponse->GetValue("Balance") . "]<br/>");
} else {
	print ("<br>Failed!!!" . "</br>");
	print ("ReturnCode   = [" . $tResponse->getReturnCode() . "]</br>");
	print ("ReturnMsg   = [" . $tResponse->getErrorMessage() . "]</br>");
}
?>