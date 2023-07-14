<?php
require_once ('../ebusclient/RegSubMerInfoRequestNew.php');

//1、生成同步二级商户及账号请求对象（新版）
$tRequest = new RegSubMerInfoRequestNew();

//2、设置请求值---与html一致，后台一次只支持一个账户提交
//$tRequest->request["MerchantID"] = ($_POST['txtMerchantID']);
$tRequest->request["SubMerId"] = ($_POST['txtSubMerId']);
$tRequest->request["SubMerName"] = ($_POST['txtSubMerName']);
$tRequest->request["SubMerType"] = ($_POST['txtSubMerType']);
$tRequest->request["CertificateType"] = ($_POST['txtCertificateType']);
$tRequest->request["CertificateNo"] = ($_POST['txtCertificateNo']);
$tRequest->request["ContactName"] = ($_POST['txtContactName']);
$tRequest->request["Address"] = ($_POST['txtAddress']);
$tRequest->request["Remark"] = ($_POST['txtRemark']);
$tRequest->request["CompanyName"] = ($_POST['txtMerchantName']);
$tRequest->request["CompanyCertType"] = ($_POST['txtMerCertificateType']);
$tRequest->request["CompanyCertNo"] = ($_POST['txtMerCertificateNum']);
$tRequest->request["AccountName"] = ($_POST['txtReceiveAccountName']);
$tRequest->request["Account"] = ($_POST['txtReceiveAccount']);
$tRequest->request["BankName"] = ($_POST['txtReceiveBankName']);
$tRequest->request["MobilePhone"] = ($_POST['txtBankMobileNum']);
$tRequest->request["MerMobilePhone"] = ($_POST['txtMerchantMobileNum']);
$tRequest->request["AccountType"] = ($_POST['txtReceiveAccountType']);
$tRequest->request["Announce"] = ($_POST['txtAnnounce']);
$tRequest->request["NotifyUrl"] = ($_POST['txtNotifyUrl']);



//3.传送交易请求
$tResponse = $tRequest->postRequest();

//3.获取请求结果
if ($tResponse->isSuccess()) {
    print ("<br>Success!!!" . "</br>");
    print ("ReturnCode   = [" . $tResponse->getReturnCode() . "]</br>");
    print ("ReturnMsg   = [" . $tResponse->getErrorMessage() . "]</br>");
    print ("MerchantID   = [" . $tResponse->GetValue("MerchantID") . "]<br/>");
    print ("TrxType = [" . $tResponse->GetValue("TrxType") . "]<br/>");
    print ("TransferNo   = [" . $tResponse->GetValue("TransferNo") . "]<br/>");
} else {
    print ("<br>Failed!!!" . "</br>");
    print ("ReturnCode   = [" . $tResponse->getReturnCode() . "]</br>");
    print ("ReturnMsg   = [" . $tResponse->getErrorMessage() . "]</br>");
}
?>