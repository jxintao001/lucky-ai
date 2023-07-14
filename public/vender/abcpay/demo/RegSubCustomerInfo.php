<?php
require_once ('../ebusclient/RegSubCustomerInfo.php');

//1、生成同步二级商户及账号请求对象（新版）
$tRequest = new RegSubCustomerInfo();

//2、设置请求值---与html一致，后台一次只支持一个账户提交
$tRequest->request["SerialNo"] = ($_POST['SerialNo']);
$tRequest->request["SubMerId"] = ($_POST['SubMerId']);
$tRequest->request["SubMerName"] = ($_POST['SubMerName']);
$tRequest->request["SubMerType"] = ($_POST['SubMerType']);
$tRequest->request["SubMerSort"] = ($_POST['SubMerSort']);
$tRequest->request["CertificateType"] = ($_POST['CertificateType']);
$tRequest->request["CertificateNo"] = ($_POST['CertificateNo']);
$tRequest->request["ContactName"] = ($_POST['ContactName']);
$tRequest->request["Account"] = ($_POST['Account']);
$tRequest->request["AccountName"] = ($_POST['AccountName']);
$tRequest->request["BankName"] = ($_POST['BankName']);
$tRequest->request["MobilePhone"] = ($_POST['MobilePhone']);
$tRequest->request["AccountType"] = ($_POST['AccountType']);
$tRequest->request["Address"] = ($_POST['Address']);
$tRequest->request["Announce"] = ($_POST['Announce']);
$tRequest->request["Remark"] = ($_POST['Remark']);
$tRequest->request["CompanyName"] = ($_POST['CompanyName']);
$tRequest->request["CompanyCertType"] = ($_POST['CompanyCertType']);
$tRequest->request["CompanyCertNo"] = ($_POST['CompanyCertNo']);
$tRequest->request["MerMobilePhone"] = ($_POST['MerMobilePhone']);
$tRequest->request["NotifyUrl"] = ($_POST['NotifyUrl']);
$tRequest->request["ReplacedAccount"] = ($_POST['ReplacedAccount']);



//3.传送交易请求
$tResponse = $tRequest->postRequest();

//3.获取请求结果
if ($tResponse->isSuccess()) {
    print ("<br>Success!!!" . "</br>");
    print ("ReturnCode   = [" . $tResponse->getReturnCode() . "]</br>");
    print ("ReturnMsg   = [" . $tResponse->getErrorMessage() . "]</br>");
    print ("SubMerSignNo   = [" . $tResponse->GetValue("SubMerSignNo") . "]<br/>");
    print ("TrxType = [" . $tResponse->GetValue("TrxType") . "]<br/>");
    print ("SerialNo   = [" . $tResponse->GetValue("SerialNo") . "]<br/>");
} else {
    print ("<br>Failed!!!" . "</br>");
    print ("ReturnCode   = [" . $tResponse->getReturnCode() . "]</br>");
    print ("ReturnMsg   = [" . $tResponse->getErrorMessage() . "]</br>");
}
?>