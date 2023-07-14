<?php
require_once ('../ebusclient/VerifyMessageCodeOrder.php');

//1、生成请求对象
$tRequest = new VerifyMessageCodeOrder();

//2、设置请求值
$tRequest->request["SubMerchantNo"] = ($_POST['txtSubMerchantNo']); //子商户号（必要信息）
$tRequest->request["VerificationCode"] = ($_POST['txtVerificationCode']); //短信验证码（与随机金额二者必输其一）
$tRequest->request["RandomAmount"] = ($_POST['txtRandomAmount']); //随机金额
$tRequest->request["Account"] = ($_POST['txtAccount']); //出金卡号（必要信息）

//3.传送交易请求
$tResponse = $tRequest->postRequest();

//4.获取请求结果
if ($tResponse->isSuccess()) {
    print ("<br>Success!!!" . "</br>");
    print ("ReturnCode   = [" . $tResponse->getReturnCode() . "]</br>");
    print ("ReturnMsg   = [" . $tResponse->getErrorMessage() . "]</br>");
} else {
    print ("<br>Failed!!!" . "</br>");
    print ("ReturnCode   = [" . $tResponse->getReturnCode() . "]</br>");
    print ("ReturnMsg   = [" . $tResponse->getErrorMessage() . "]</br>");
}