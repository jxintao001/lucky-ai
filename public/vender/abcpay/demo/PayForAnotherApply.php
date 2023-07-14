<?php
require_once ('../ebusclient/PayForAnotherApply.php');
//1、生成内转交易请求对象
$tRequest = new PayForAnotherApply();
$tRequest->request["SubMerId"] = ($_POST['SubMerId']); //二级商户编号
$tRequest->request["Account"] = ($_POST['Account']); //收款账号
$tRequest->request["AccountName"] = ($_POST['AccountName']); //收款账户名
$tRequest->request["TrxAmount"] = ($_POST['TrxAmount']); //出金金额
$tRequest->request["DrawingFlag"] = ($_POST['DrawingFlag']); //是否关联支付订单编号
$tRequest->request["OrderNo"] = ($_POST['OrderNo']); //关联的支付订单号
$tRequest->request["Remark"] = ($_POST['Remark']); //附言
$tRequest->request["RecBankNo"] = ($_POST['RecBankNo']); //他行行号

//2、传送内转交易并取得退货结果
$tResponse = $tRequest->postRequest();

//3、内转交易请求提交成功，返回结果信息
if ($tResponse->isSuccess()) {
	print ("<br>Success!!!" . "</br>");
	print ("ReturnCode   = [" . $tResponse->getReturnCode() . "]</br>");
	print ("ReturnMsg   = [" . $tResponse->getErrorMessage() . "]</br>");
    print ("OrderNo   = [" . $tResponse->GetValue("OrderNo") . "]<br/>");
} else {
	print ("<br>Failed!!!" . "</br>");
	print ("ReturnCode   = [" . $tResponse->getReturnCode() . "]</br>");
	print ("ReturnMsg   = [" . $tResponse->getErrorMessage() . "]</br>");
}
?>