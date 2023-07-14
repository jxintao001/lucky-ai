<?php
require_once ('../ebusclient/EntrustSignResendSMS.php');
//1、生成内转交易请求对象
$tRequest = new EntrustSignResendSMS();
$tRequest->request["SignNo"] = ($_POST['SignNo']); //签约编号
$tRequest->request["CustomAccNo"] = ($_POST['CustomAccNo']); //客户签约卡号

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