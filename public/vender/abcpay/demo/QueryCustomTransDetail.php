<?php
require_once ('../ebusclient/QrySubCustomTransDetail.php');


//1、生成请求对象
$tRequest = new QrySubCustomTransDetail();

//2、设置请求值
$tRequest->request["SubCustomNo"] = ($_POST['txtSubMerchantNo']); //子商户号
$tRequest->request["selType"] = ($_POST['txtSelType']);
$tRequest->request["Status"] = ($_POST['txtStatus']);
$tRequest->request["sDate"] = ($_POST['txtsDate']);
$tRequest->request["eDate"] = ($_POST['txteDate']);
$tRequest->request["OrderNo"] = ($_POST['txtOrderNo']);


//3.传送交易请求
$tResponse = $tRequest->postRequest();

//4.获取请求结果
if ($tResponse->isSuccess()) {
    print ("<br>Success!!!" . "</br>");
    print ("ReturnCode   = [" . $tResponse->getReturnCode() . "]</br>");
    print ("ReturnMsg   = [" . $tResponse->getErrorMessage() . "]</br>");

    //print ("MerchantName   = [" . iconv('GB2312','utf-8',$tResponse->GetValue("MerchantName")) . "]</br>");
    //print ("SubMerId   = [" . iconv('GB2312','utf-8',$tResponse->GetValue("SubMerId")) . "]</br>");
    //  print ("SubMerName   = [" . $tResponse->GetValue("SubMerName") . "]</br>");
    print ("TrxList   = [" . iconv('GB2312','utf-8',$tResponse->GetValue("TrxList")) . "]</br>");

} else {
    print ("<br>Failed!!!" . "</br>");
    print ("ReturnCode   = [" . $tResponse->getReturnCode() . "]</br>");
    print ("ReturnMsg   = [" . $tResponse->getErrorMessage() . "]</br>");
}

