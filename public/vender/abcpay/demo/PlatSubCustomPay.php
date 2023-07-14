<?php
require_once ('../ebusclient/PlatSubCustomPay.php');
//1、生成内转交易请求对象
$tRequest = new PlatSubCustomPay();
$tRequest->request["OrderNo"] = ($_POST['OrderNo']); //交易流水号
$tRequest->request["PlatSubCustomNo"] = ($_POST['PlatSubCustomNo']); //转出方二级客户编号
$tRequest->request["PlatSubMerchantNo"] = ($_POST['PlatSubMerchantNo']); //转入方二级商户编号
$tRequest->request["Amount"] = ($_POST['Amount']); //交易金额
$tRequest->request["Remark"] = ($_POST['Remark']); //附言

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