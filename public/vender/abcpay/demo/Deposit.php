<?php
require_once('../ebusclient/Deposit.php');
//1、生成订单对象，并将订单明细加入订单中
$tRequest = new Deposit();
//2、设定订单属性
$tRequest->order["orderTimeoutDate"] = ($_POST['txtorderTimeoutDate']);
$tRequest->order["OrderNo"] = ($_POST['txtOrderNo']);
$tRequest->order["OrderAmount"] = ($_POST['txtOrderAmount']);
$tRequest->order["OrderDesc"] = ($_POST['txtOrderDesc']);  //订单说明

//3、设定支付请求对象
$tRequest->request["PaymentLinkType"] = ($_POST['txtPaymentLinkType']);
$tRequest->request["NotifyType"] = ($_POST['txtNotifyType']);
$tRequest->request["ResultNotifyURL"] = ($_POST['txtResultNotifyURL']);
$tRequest->request["MerchantRemarks"] = ($_POST['txtMerchantRemarks']);
$tRequest->request["ReceiveSubMerchantNo"] = ($_POST['txtReceiveSubMerchantNo']);

//4、传送支付请求并返回结果
$tResponse = $tRequest->postRequest();


//3、支付请求提交成功，返回结果信息
if ($tResponse->isSuccess()) {
    print ("<br>Success!!!" . "</br>");
    print ("ReturnCode   = [" . $tResponse->getReturnCode() . "]</br>");
    print ("ReturnMsg   = [" . $tResponse->getErrorMessage() . "]</br>");
}
else
{
    print("<br>Failed!!!"."</br>");
    print ("ReturnCode   = [" . $tResponse->getReturnCode() . "]</br>");
    print ("ReturnMsg   = [" . $tResponse->getErrorMessage() . "]</br>");
}