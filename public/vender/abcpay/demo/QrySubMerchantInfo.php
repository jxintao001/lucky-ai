<?php
require_once ('../ebusclient/QrySubMerInfo.php');


//1、生成请求对象
$tRequest = new QrySubMerInfo();

//2、设置请求值
$tRequest->request["SubMerId"] = ($_POST['txtSubMerchantNo']); //子商户号


//3.传送交易请求
$tResponse = $tRequest->postRequest();

//4.获取请求结果
if ($tResponse->isSuccess()) {
    print ("<br>Success!!!" . "</br>");
    print ("ReturnCode   = [" . $tResponse->getReturnCode() . "]</br>");
    print ("ReturnMsg   = [" . $tResponse->getErrorMessage() . "]</br>");

    print ("MerchantName   = [" . iconv('GB2312','utf-8',$tResponse->GetValue("MerchantName")) . "]</br>");
    print ("SubMerId   = [" . iconv('GB2312','utf-8',$tResponse->GetValue("SubMerId")) . "]</br>");
  //  print ("SubMerName   = [" . $tResponse->GetValue("SubMerName") . "]</br>");
    print ("SubMerName   = [" . iconv('GB2312','utf-8',$tResponse->GetValue("SubMerName")) . "]</br>");
    print ("SubMerSort   = [" . iconv('GB2312','utf-8',$tResponse->GetValue("SubMerSort")). "]</br>");
    print ("SubMerchantType   = [" . iconv('GB2312','utf-8',$tResponse->GetValue("SubMerchantType")) . "]</br>");
    print ("CertificateType   = [" . $tResponse->GetValue("CertificateType") . "]</br>");
    print ("CertificateNo   = [" . $tResponse->GetValue("CertificateNo") . "]</br>");
    print ("ContactName   = [" . iconv('GB2312','utf-8',$tResponse->GetValue("ContactName")) . "]</br>");
    print ("MobileNo   = [" . $tResponse->GetValue("MobileNo") . "]</br>");
    print ("StatusMessage   = [" . iconv('GB2312','utf-8',$tResponse->GetValue("StatusMessage")) . "]</br>");
    print ("CompanyCertType   = [" . $tResponse->GetValue("CompanyCertType") . "]</br>");
    print ("CompanyCertNo   = [" . $tResponse->GetValue("CompanyCertNo") . "]</br>");
    print ("CompanyName   = [" . iconv('GB2312','utf-8',$tResponse->GetValue("CompanyName")) . "]</br>");
    print ("NotifyUrl   = [" . $tResponse->GetValue("NotifyUrl") . "]</br>");
    print ("SubMerSignNo   = [" . $tResponse->GetValue("SubMerSignNo") . "]</br>");
    print ("isPassed   = [" . $tResponse->GetValue("isPassed") . "]</br>");
    print ("Status   = [" . $tResponse->GetValue("Status") . "]<br/>");
} else {
    print ("<br>Failed!!!" . "</br>");
    print ("ReturnCode   = [" . $tResponse->getReturnCode() . "]</br>");
    print ("ReturnMsg   = [" . $tResponse->getErrorMessage() . "]</br>");
}
