<?php
require_once ('../ebusclient/EntrustUnSign.php');
//1、生成内转交易请求对象
$tRequest = new EntrustUnSign();
$tRequest->request["SignNo"] = ($_POST['SignNo']); //签约编号
$tRequest->request["OrderNo"] = ($_POST['OrderNo']); //流水号

//2、传送内转交易并取得退货结果
$tResponse = $tRequest->postRequest();

//3、内转交易请求提交成功，返回结果信息
if ($tResponse->isSuccess()) {
	print ("<br>Success!!!" . "</br>");
	print ("ReturnCode   = [" . $tResponse->getReturnCode() . "]</br>");
	print ("ReturnMsg   = [" . $tResponse->getErrorMessage() . "]</br>");
    print ("TrxType   = [" . $tResponse->GetValue("TrxType") . "]</br>");
    print ("SignNo   = [" . $tResponse->GetValue("SignNo") . "]</br>");
} else {
	print ("<br>Failed!!!" . "</br>");
	print ("ReturnCode   = [" . $tResponse->getReturnCode() . "]</br>");
	print ("ReturnMsg   = [" . $tResponse->getErrorMessage() . "]</br>");
}
?>