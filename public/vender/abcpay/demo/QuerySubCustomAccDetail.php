<?php
require_once ('../ebusclient/QrySubCustomAccDetail.php');


//1、生成请求对象
$tRequest = new QrySubCustomAccDetail();

//2、设置请求值
$tRequest->request["SubCustomNo"] = ($_POST['txtSubMerchantNo']); //子商户号
$tRequest->request["AccDate"] = ($_POST['txtAccDate']);
$tRequest->request["NumEntryRec"] = ($_POST['txtNumEntryRec']);
$tRequest->request["JrnNo"] = ($_POST['txtJrnNo']);


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
    print ("DetailTable   = [" . iconv('GB2312','utf-8',$tResponse->GetValue("DetailTable")) . "]</br>");
    print ("NextPage   = [" . iconv('GB2312','utf-8',$tResponse->GetValue("NextPage")) . "]</br>");
    print ("NumEntryRec   = [" . iconv('GB2312','utf-8',$tResponse->GetValue("NumEntryRec")) . "]</br>");
    print ("JrnNo   = [" . iconv('GB2312','utf-8',$tResponse->GetValue("JrnNo")) . "]</br>");

} else {
    print ("<br>Failed!!!" . "</br>");
    print ("ReturnCode   = [" . $tResponse->getReturnCode() . "]</br>");
    print ("ReturnMsg   = [" . $tResponse->getErrorMessage() . "]</br>");
}
