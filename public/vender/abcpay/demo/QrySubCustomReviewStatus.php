<?php
require_once ('../ebusclient/QrySubCustomReviewStatus.php');
//1、生成内转交易请求对象
$tRequest = new QrySubCustomReviewStatus();
$tRequest->request["SubCustomNo"] = ($_POST['SubCustomNo']); //二级客户编号
$tRequest->request["SerialNo"] = ($_POST['SerialNo']); //申请单号

//2、传送内转交易并取得退货结果
$tResponse = $tRequest->postRequest();

//3、内转交易请求提交成功，返回结果信息
if ($tResponse->isSuccess()) {
	print ("<br>Success!!!" . "</br>");
	print ("ReturnCode   = [" . $tResponse->getReturnCode() . "]</br>");
	print ("ReturnMsg   = [" . $tResponse->getErrorMessage() . "]</br>");
    print ("Account   = [" . $tResponse->GetValue("Account") . "]</br>");
    print ("isPassed   = [" . $tResponse->GetValue("isPassed") . "]</br>");
} else {
	print ("<br>Failed!!!" . "</br>");
	print ("ReturnCode   = [" . $tResponse->getReturnCode() . "]</br>");
	print ("ReturnMsg   = [" . $tResponse->getErrorMessage() . "]</br>");
}
?>