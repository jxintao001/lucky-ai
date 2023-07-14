<?php
require_once ('../ebusclient/InnerPaymentRequest.php');
//1、生成内转交易请求对象
$tRequest = new InnerPaymentRequest();
$tRequest->request["InternalTransferNo"] = ($_POST['InternalTransferNo']); //交易编号
$tRequest->request["RemitterSubMerchantNo"] = ($_POST['RemitterSubMerchantNo']); //转出方二级商户编号
$tRequest->request["RemitteeSubMerchantNo"] = ($_POST['RemitteeSubMerchantNo']); //转入方二级商户编号
$tRequest->request["Amount"] = ($_POST['TrxAmount']); //金额

//2、传送内转交易并取得退货结果
$tResponse = $tRequest->postRequest();

//3、内转交易请求提交成功，返回结果信息
if ($tResponse->isSuccess()) {
	print ("<br>Success!!!" . "</br>");
	print ("ReturnCode   = [" . $tResponse->getReturnCode() . "]</br>");
	print ("ReturnMsg   = [" . $tResponse->getErrorMessage() . "]</br>");
} else {
	print ("<br>Failed!!!" . "</br>");
	print ("ReturnCode   = [" . $tResponse->getReturnCode() . "]</br>");
	print ("ReturnMsg   = [" . $tResponse->getErrorMessage() . "]</br>");
}
?>