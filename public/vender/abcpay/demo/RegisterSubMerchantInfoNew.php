<?php
require_once ('../ebusclient/RegisterSubMerchantInfo.php');

//1、生成同步二级商户及账号请求对象（新版）
$tRequest = new RegisterSubMerchantInfo();

//2、设置请求值---与html一致，后台一次只支持一个账户提交
$tRequest->request["SubMerId"] = ($_POST['SubMerId']);
$tRequest->request["IsNewFlag"] = ($_POST['SubMerFlag']);
$tRequest->request["SubMerName"] = ($_POST['SubMerName']);
$tRequest->request["SubMerchantShortName"] = ($_POST['SubMerchantShortName']);
$tRequest->request["ServicePhone"] = ($_POST['ServicePhone']);
$tRequest->request["Industry"] = ($_POST['Industry']);
$tRequest->request["BusinessRange"] = ($_POST['BusinessRange']);
$tRequest->request["Address"] = ($_POST['Address']);
$tRequest->request["SubMerType"] = ($_POST['SubMerType']);
$tRequest->request["CompanyCertType"] = ($_POST['MerCertificateType']);
$tRequest->request["CompanyCertNo"] = ($_POST['MerCertificateNum']);
$tRequest->request["EndCertificateValidity"] = ($_POST['EndCertificateValidity']);
$tRequest->request["SubMerClass"] = ($_POST['SubMerClass']);
$tRequest->request["ContactName"] = ($_POST['ContactName']);
$tRequest->request["CertificateType"] = ($_POST['CertificateType']);
$tRequest->request["CertificateNo"] = ($_POST['CertificateNo']);
$tRequest->request["CertificateBegDate"] = ($_POST['CertificateBegDate']);
$tRequest->request["FrCertEndDate"] = ($_POST['FrCertEndDate']);
$tRequest->request["FrResidence"] = ($_POST['FrResidence']);
$tRequest->request["FrIsController"] = ($_POST['FrIsController']);
$tRequest->request["ControllerName"] = ($_POST['ControllerName']);
$tRequest->request["ControllerCertType"] = ($_POST['ControllerCertType']);
$tRequest->request["ControllerCertNo"] = ($_POST['ControllerCertNo']);
$tRequest->request["ControllerCertBegDate"] = ($_POST['ControllerCertBegDate']);
$tRequest->request["ControllerCertEndDate"] = ($_POST['ControllerCertEndDate']);
$tRequest->request["ControllerResidence"] = ($_POST['ControllerResidence']);
$tRequest->request["FrIsAgent"] = ($_POST['FrIsAgent']);
$tRequest->request["AgentName"] = ($_POST['AgentName']);
$tRequest->request["AgentCertType"] = ($_POST['AgentCertType']);
$tRequest->request["AgentCertNo"] = ($_POST['AgentCertNo']);
$tRequest->request["AgentCertBegDate"] = ($_POST['AgentCertBegDate']);
$tRequest->request["AgentCertEndDate"] = ($_POST['AgentCertEndDate']);
$tRequest->request["AgentResidence"] = ($_POST['AgentResidence']);
$tRequest->request["SubMerContactName"] = ($_POST['SubMerContactName']);
$tRequest->request["SubMerContactCert"] = ($_POST['SubMerContactCert']);
$tRequest->request["MerMobilePhone"] = ($_POST['SubMerMobileNum']);
$tRequest->request["SubMerContactMail"] = ($_POST['SubMerContactMail']);
$tRequest->request["SubMerContactType"] = ($_POST['SubMerContactType']);
$tRequest->request["Account"] = ($_POST['Account']);
$tRequest->request["ReplacedAccount"] = ($_POST['ReplacedAccount']);
$tRequest->request["AccountName"] = ($_POST['AccountName']);
$tRequest->request["BankName"] = ($_POST['BankName']);
$tRequest->request["MobilePhone"] = ($_POST['MobilePhone']);
$tRequest->request["AccountType"] = ($_POST['AccountType']);
$tRequest->request["ApplyService"] = ($_POST['ApplyService']);
$tRequest->request["Announce"] = ($_POST['Announce']);
$tRequest->request["Remark"] = ($_POST['Remark']);
$tRequest->request["NotifyUrl"] = ($_POST['NotifyUrl']);










//3.传送交易请求
$tResponse = $tRequest->postRequest();

//3.获取请求结果
if ($tResponse->isSuccess()) {
    print ("<br>Success!!!" . "</br>");
    print ("ReturnCode   = [" . $tResponse->getReturnCode() . "]</br>");
    print ("ReturnMsg   = [" . $tResponse->getErrorMessage() . "]</br>");
    print ("MerchantID   = [" . $tResponse->GetValue("MerchantID") . "]<br/>");
    print ("SubMerId   = [" . $tResponse->GetValue("SubMerId") . "]<br/>");
    print ("SubMerSignNo   = [" . $tResponse->GetValue("SubMerSignNo") . "]<br/>");
    print ("TrxType = [" . $tResponse->GetValue("TrxType") . "]<br/>");
    print ("SerialNo   = [" . $tResponse->GetValue("SerialNo") . "]<br/>");
} else {
    print ("<br>Failed!!!" . "</br>");
    print ("ReturnCode   = [" . $tResponse->getReturnCode() . "]</br>");
    print ("ReturnMsg   = [" . $tResponse->getErrorMessage() . "]</br>");
}