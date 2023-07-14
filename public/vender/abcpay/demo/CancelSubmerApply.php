<?php
require_once ('../ebusclient/CancelSubmerApply.php');

//1、生成二级商户同步请求撤销对象
$tRequest = new CancelSubmerApply();

//2、设置请求值---与html一致，后台一次只支持一个账户提交
//$tRequest->request["MerchantID"] = ($_POST['txtMerchantID']);
$tRequest->request["SerialNumber"] = ($_POST['SerialNumber']);
$tRequest->request["SubMerchantNo"] = ($_POST['SubMerchantNo']);

//3.传送交易请求
$tResponse = $tRequest->postRequest();

//3.获取请求结果
if ($tResponse->isSuccess()) {
    print ("<br>Success!!!" . "</br>");
    print ("ReturnCode   = [" . $tResponse->getReturnCode() . "]</br>");
    print ("ReturnMsg   = [" . $tResponse->getErrorMessage() . "]</br>");
    print ("MerchantID   = [" . $tResponse->GetValue("MerchantID") . "]<br/>");
    print ("TrxType = [" . $tResponse->GetValue("TrxType") . "]<br/>");
    print ("SubMerchantNo   = [" . $tResponse->GetValue("SubMerchantNo") . "]<br/>");
    print ("SerialNumber   = [" . $tResponse->GetValue("SerialNumber") . "]<br/>");
} else {
    print ("<br>Failed!!!" . "</br>");
    print ("ReturnCode   = [" . $tResponse->getReturnCode() . "]</br>");
    print ("ReturnMsg   = [" . $tResponse->getErrorMessage() . "]</br>");
}
?>