<?php
require_once ('../ebusclient/RegSubMerTerminalInfo.php');

//1、生成同步二级商户及账号请求对象（新版）
$tRequest = new RegSubMerTerminalInfo();

//2、设置请求值---与html一致，后台一次只支持一个账户提交
$tRequest->request["SubMerchantNo"] = ($_POST['SubMerchantNo']);
$tRequest->request["ShopName"] = ($_POST['ShopName']);
$tRequest->request["DeviceSource"] = ($_POST['DeviceSource']);
$tRequest->request["DeviceType"] = ($_POST['DeviceType']);
$tRequest->request["DeviceSeqId"] = ($_POST['DeviceSeqId']);
$tRequest->request["DeviceManufacturer"] = ($_POST['DeviceManufacturer']);
$tRequest->request["DeviceModel"] = ($_POST['DeviceModel']);
$tRequest->request["EnablePosition"] = ($_POST['EnablePosition']);
$tRequest->request["DeviceAddress"] = ($_POST['DeviceAddress']);
$tRequest->request["DeviceLongitude"] = ($_POST['DeviceLongitude']);
$tRequest->request["DeviceLatitude"] = ($_POST['DeviceLatitude']);
$tRequest->request["DeviceState"] = ($_POST['DeviceState']);

//3.传送交易请求
$tResponse = $tRequest->postRequest();

//3.获取请求结果
if ($tResponse->isSuccess()) {
    print ("<br>Success!!!" . "</br>");
    print ("ReturnCode   = [" . $tResponse->getReturnCode() . "]</br>");
    print ("ReturnMsg   = [" . $tResponse->getErrorMessage() . "]</br>");
    print ("MerchantID   = [" . $tResponse->GetValue("MerchantID") . "]<br/>");
    print ("DeviceId   = [" . $tResponse->GetValue("DeviceId") . "]<br/>");
    print ("SubMerchantNo   = [" . $tResponse->GetValue("SubMerchantNo") . "]<br/>");
} else {
    print ("<br>Failed!!!" . "</br>");
    print ("ReturnCode   = [" . $tResponse->getReturnCode() . "]</br>");
    print ("ReturnMsg   = [" . $tResponse->getErrorMessage() . "]</br>");
}