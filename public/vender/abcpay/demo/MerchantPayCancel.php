<?php
require_once ('../ebusclient/PayCancelRequest.php');
//1、生成商户对账单下载请求对象
$tRequest = new PayCancelRequest();
$tRequest->request["OrderNo"] = ($_POST['OrderNo']);    //订单编号
$tRequest->request["SubMchNO"] = ($_POST['SubMchNO']);  //二级商户号
$tRequest->request["ModelFlag"] = ($_POST['ModelFlag']);    //支付模式
$tRequest->request["MerchantFlag"] = ($_POST['MerchantFlag']);  //支付渠道

//2、传送商户对账单下载请求并取得对账单
$tResponse = $tRequest->postRequest();

//3、判断商户对账单下载结果状态，进行后续操作
if ($tResponse->isSuccess()) {
	print ("<br>Success!!!" . "</br>");
	print ("ReturnCode   = [" . $tResponse->getReturnCode() . "]</br>");
	print ("ReturnMsg   = [" . $tResponse->getErrorMessage() . "]</br>");
	print ("OrderInfo      = [" . $tResponse->GetValue("OrderInfo") . "]<br/>");
} else {
	print ("<br>Failed!!!" . "</br>");
	print ("ReturnCode   = [" . $tResponse->getReturnCode() . "]</br>");
	print ("ReturnMsg   = [" . $tResponse->getErrorMessage() . "]</br>");
	print ("OrderInfo      = [" . $tResponse->GetValue("OrderInfo") . "]<br/>");
}
?>