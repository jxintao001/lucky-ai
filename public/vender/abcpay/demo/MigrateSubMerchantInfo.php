<?php
require_once ('../ebusclient/MigrateSubMerInfo.php');

//1、生成请求对象
$tRequest = new MigrateSubMerInfo();

//2、设置请求值
$tRequest->request["FromMerchantNo"] = ($_POST['txtFromMerchantNo']);
$tRequest->request["SubMerchantNo"] = ($_POST['txtSubMerchantNo']);


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
?>