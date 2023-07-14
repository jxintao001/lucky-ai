<?php
require_once ('../ebusclient/QrySubMerSettleAmountRequest.php');

//1、生成请求对象
$tRequest = new QrySubMerSettleAmountRequest();

//2、设置请求值
$tRequest->request["MerchantNo"] = ($_POST['MerchantNo']); //商e付商户号 （必要信息）
$tRequest->request["SubMerchantNo"] = ($_POST['SubMerchantNo']); //子商户号（必要信息）
$tRequest->request["TrxDate"] = ($_POST['TrxDate']); //日期 （必要信息）


//3.传送交易请求
$tResponse = $tRequest->postRequest();

//4.获取请求结果
if ($tResponse->isSuccess()) {
    print ("<br>Success!!!" . "</br>");
    print ("ReturnCode   = [" . $tResponse->getReturnCode() . "]</br>");
    print ("ReturnMsg   = [" . $tResponse->getErrorMessage() . "]</br>");
    print ("SubMerchantNo   = [" . $tResponse->GetValue("SubMerchantNo") . "]</br>");
    print ("MerchantNo   = [" . $tResponse->GetValue("MerchantID") . "]</br>");
    print ("TrxDate   = [" . $tResponse->GetValue("TrxDate") . "]</br>");
    print ("Amount   = [" . $tResponse->GetValue("Amount") . "]</br>");
    print ("CanConfirmAmount   = [" . $tResponse->GetValue("CanConfirmAmount") . "]</br>");
    print ("SerialNo   = [" . $tResponse->GetValue("SerialNo") . "]</br>");

} else {
    print ("<br>Failed!!!" . "</br>");
    print ("ReturnCode   = [" . $tResponse->getReturnCode() . "]</br>");
    print ("ReturnMsg   = [" . $tResponse->getErrorMessage() . "]</br>");
}
?>