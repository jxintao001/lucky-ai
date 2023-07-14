<?php
require_once ('../ebusclient/QueryRealTimeRegister.php');
//1、生成内转交易请求对象
$tRequest = new QueryRealTimeRegister();
$tRequest->request["RequestNo"] = ($_POST['RequestNo']); //请求流水号
$tRequest->request["OrderNo"] = ($_POST['OrderNo']); //交易编号
$tRequest->request["SubMerchantNo"] = ($_POST['SubMerchantNo']); //二级商户号

//2、传送内转交易并取得退货结果
$tResponse = $tRequest->postRequest();

//3、内转交易请求提交成功，返回结果信息
if ($tResponse->isSuccess()) {
	print ("<br>Success!!!" . "</br>");
	print ("ReturnCode   = [" . $tResponse->getReturnCode() . "]</br>");
	print ("ReturnMsg   = [" . $tResponse->getErrorMessage() . "]</br>");
    print ("TrxType   = [" . $tResponse->GetValue("TrxType") . "]</br>");
    print ("MerchantNo   = [" . $tResponse->GetValue("MerchantNo") . "]</br>");
    print ("BatchNo   = [" . $tResponse->GetValue("RequestNo") . "]</br>");
    print ("OrderNo   = [" . $tResponse->GetValue("OrderNo") . "]</br>");
    print ("SubMerchantNo   = [" . $tResponse->GetValue("SubMerchantNo") . "]</br>");
    print ("Status   = [" . $tResponse->GetValue("Status") . "]</br>");
    print ("FailedReason   = [" . $tResponse->GetValue("FailedReason") . "]</br>");
} else {
	print ("<br>Failed!!!" . "</br>");
	print ("ReturnCode   = [" . $tResponse->getReturnCode() . "]</br>");
	print ("ReturnMsg   = [" . $tResponse->getErrorMessage() . "]</br>");
}
?>