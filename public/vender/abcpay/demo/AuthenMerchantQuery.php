<?php
require_once ('../ebusclient/AuthenMerchantQueryRequest.php');

//1、生成同步二级商户及账号请求对象
$tRequest = new AuthenMerchantQueryRequest();

//2、设置请求值
$tRequest->request["TransferNo"] = ($_POST['TransferNo']);

//3.传送交易请求
$tResponse = $tRequest->postRequest();

//3.获取请求结果
if ($tResponse->isSuccess()) {
	print ("<br>Success!!!" . "</br>");
	print ("ReturnCode   = [" . $tResponse->getReturnCode() . "]</br>");
	print ("ReturnMsg   = [" . $tResponse->getErrorMessage() . "]</br>");
	print ("Status   = [" . $tResponse->GetValue("Status") . "]<br/>");
} else {
	print ("<br>Failed!!!" . "</br>");
	print ("ReturnCode   = [" . $tResponse->getReturnCode() . "]</br>");
	print ("ReturnMsg   = [" . $tResponse->getErrorMessage() . "]</br>");
	print ("Status   = [" . $tResponse->GetValue("Status") . "]<br/>");
}
?>