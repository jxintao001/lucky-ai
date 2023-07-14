<?php
require_once ('../ebusclient/ConfirmPOSPaymentRequest.php');

//1、生成请求对象
$tRequest = new ConfirmPOSPaymentRequest();

//2、设置请求值
$tRequest->request["OrderDate"] = ($_POST['txtOrderDate']);
$tRequest->request["OrderNo"] = ($_POST['txtOrderNo']);
$tRequest->request["SubMerchantNo"] = ($_POST['txtSubMerchantNo']);
$tRequest->request["SequenceNo"] = ($_POST['txtSeqNo']);
$tRequest->request["Amount"] = ($_POST['txtOrderAmount']);


//3.传送交易请求
$tResponse = $tRequest->postRequest();

//4.获取请求结果
if ($tResponse->isSuccess()) {
    print ("<br>Success!!!" . "</br>");
    print ("ReturnCode   = [" . $tResponse->getReturnCode() . "]</br>");
    print ("ReturnMsg   = [" . $tResponse->getErrorMessage() . "]</br>");

    print ("Status   = [" . $tResponse->GetValue("Status") . "]</br>");

} else {
    print ("<br>Failed!!!" . "</br>");
    print ("ReturnCode   = [" . $tResponse->getReturnCode() . "]</br>");
    print ("ReturnMsg   = [" . $tResponse->getErrorMessage() . "]</br>");
}
