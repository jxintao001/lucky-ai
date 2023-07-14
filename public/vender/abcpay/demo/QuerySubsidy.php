<?php
require_once ('../ebusclient/QuerySubsidy.php');
//1、生成内转交易请求对象
$tRequest = new QuerySubsidy();
$tRequest->request["InternalTransferNo"] = ($_POST['InternalTransferNo']); //内转号

//2、传送内转交易并取得退货结果
$tResponse = $tRequest->postRequest();

//3、内转交易请求提交成功，返回结果信息
if ($tResponse->isSuccess()) {
	print ("<br>Success!!!" . "</br>");
	print ("ReturnCode   = [" . $tResponse->getReturnCode() . "]</br>");
	print ("ReturnMsg   = [" . $tResponse->getErrorMessage() . "]</br>");
    print ("ResultCode   = [" . $tResponse->GetValue("ResultCode") . "]</br>");
    print ("ResultMessage   = [" . iconv('GB2312','utf-8',$tResponse->GetValue("ResultMessage")) . "]</br>");
    print ("OriginalOrderNo   = [" . $tResponse->GetValue("OriginalOrderNo") . "]</br>");
    print ("TransType   = [" . $tResponse->GetValue("TransType") . "]</br>");
    print ("AccDate   = [" . $tResponse->GetValue("AccDate") . "]</br>");
    print ("JrnNo   = [" . $tResponse->GetValue("JrnNo") . "]</br>");
    print ("Amount   = [" . $tResponse->GetValue("Amount") . "]</br>");
} else {
	print ("<br>Failed!!!" . "</br>");
	print ("ReturnCode   = [" . $tResponse->getReturnCode() . "]</br>");
	print ("ReturnMsg   = [" . $tResponse->getErrorMessage() . "]</br>");
}
?>