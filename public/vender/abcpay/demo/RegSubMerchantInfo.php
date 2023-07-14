<?php
require_once ('../ebusclient/RegSubMerchantInfoRequest.php');

//1、生成同步二级商户及账号请求对象
$tRequest = new RegSubMerchantInfoRequest();

//2、设置请求值---与html一致，后台一次只支持一个账户提交 modified by chj @20190305
//$tRequest->request["MerchantID"] = ($_POST['txtMerchantID']);
$tRequest->request["SubMerId"] = ($_POST['txtSubMerId']);
$tRequest->request["SubMerName"] = ($_POST['txtSubMerName']);
$tRequest->request["SubMerType"] = ($_POST['txtSubMerType']);
$tRequest->request["Status"] = ($_POST['txtStatus']);
//$tRequest->request["SubMerSort"] = ($_POST['txtSubMerSort']);
$tRequest->request["SubMerSort"] = "1";
//$tRequest->request["MCC"] = ($_POST['txtMCC']);
$tRequest->request["ContactName"] = ($_POST['txtContactName']);
$tRequest->request["CertificateType"] = ($_POST['txtCertificateType']);
$tRequest->request["CertificateNo"] = ($_POST['txtCertificateNo']);

//$tRequest->request["MobileNo"] = ($_POST['txtMobileNo']);
$tRequest->request["CompanyName"] = ($_POST['txtMerchantName']);
$tRequest->request["CompanyCertType"] = ($_POST['txtMerCertificateType']);
$tRequest->request["CompanyCertNo"] = ($_POST['txtMerCertificateNum']);

$tRequest->request["AccountName"] = ($_POST['txtReceiveAccountName']);
$tRequest->request["Account"] = ($_POST['txtReceiveAccount']);
$tRequest->request["BankName"] = ($_POST['txtReceiveBankName']);

$tRequest->request["MobilePhone"] = ($_POST['txtBankMobileNum']);
$tRequest->request["AccountType"] = ($_POST['txtReceiveAccountType']);
$tRequest->request["Address"] = ($_POST['txtAddress']);
$tRequest->request["Remark"] = ($_POST['txtRemark']);


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