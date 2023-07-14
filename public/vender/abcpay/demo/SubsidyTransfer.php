<?php
require_once ('../ebusclient/SubsidyTransfer.php');
//1、生成内转交易请求对象
$tRequest = new SubsidyTransfer();
$tRequest->request["OrderNo"] = ($_POST['OrderNo']); //订单号
$tRequest->request["InternalTransferNo"] = ($_POST['InternalTransferNo']); //内转号
$tRequest->request["RemitterSubMerchantNo"] = ($_POST['RemitterSubMerchantNo']); //出金账子户
$tRequest->request["RemitteeSubMerchantNo"] = ($_POST['RemitteeSubMerchantNo']); //入金子账户
$tRequest->request["Amount"] = ($_POST['Amount']); //补贴金额

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