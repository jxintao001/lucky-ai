<?php
require_once ('../ebusclient/POSThirdFileDownload.php');

//1、生成请求对象
$tRequest = new POSThirdFileDownload();

//2、设置请求值
$tRequest->request["MerchantNo"] = ($_POST['MerchantNo']); //商e付商户号 （必要信息）
$tRequest->request["SubMerchantNo"] = ($_POST['SubMerchantNo']); //子商户号（必要信息）
$tRequest->request["FileDate"] = ($_POST['FileDate']); //日期 （必要信息）


//3.传送交易请求
$tResponse = $tRequest->postRequest();

//4.获取请求结果
if ($tResponse->isSuccess()) {
    print ("<br>Success!!!" . "</br>");
    print ("ReturnCode   = [" . $tResponse->getReturnCode() . "]</br>");
    print ("ReturnMsg   = [" . $tResponse->getErrorMessage() . "]</br>");

    print ("TrxType   = [" . $tResponse->GetValue("TrxType") . "]</br>");
    print ("FileDate   = [" . $tResponse->GetValue("FileDate") . "]</br>");
    print ("DetailRecords   = [" . $tResponse->GetValue("DetailRecords") . "]</br>");

} else {
    print ("<br>Failed!!!" . "</br>");
    print ("ReturnCode   = [" . $tResponse->getReturnCode() . "]</br>");
    print ("ReturnMsg   = [" . $tResponse->getErrorMessage() . "]</br>");
}
?>