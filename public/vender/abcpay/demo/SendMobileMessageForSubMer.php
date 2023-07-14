<?php
header("charset=UTF-8");
require_once ('../ebusclient/SubMerMobileMessage.php');

//1、生成请求对象
$tRequest = new SubMerMobileMessage();

//2、设置请求值
$tRequest->request["SubMerId"] = ($_POST['txtSubMerchantNo']); //子商户号
$tRequest->request["SubMerchantAccNo"] = ($_POST['txtSubMerchantAccNo']); //出金账号

//3.传送查询
$tResponse = $tRequest->postRequest();

//4.获取请求结果,判断查询结果状态，进行后续操作
if ($tResponse->isSuccess()) {
    print ("<br>Success!!!" . "</br>");
    print ("ReturnCode   = [" . $tResponse->getReturnCode() . "]</br>");
    print ("ReturnMsg   = [" . $tResponse->getErrorMessage() . "]</br>");
    print ("TrxType   = [" . $tResponse->GetValue("TrxType") . "]</br>");
    print ("Status   = [" . $tResponse->GetValue("Status") . "]<br/>");
} else {
    print ("<br>Failed!!!" . "</br>");
    print ("ReturnCode   = [" . $tResponse->getReturnCode() . "]</br>");
    print ("ReturnMsg   = [" . $tResponse->getErrorMessage() . "]</br>");
    print ("Status   = [" . $tResponse->GetValue("Status") . "]<br/>");
}
?>