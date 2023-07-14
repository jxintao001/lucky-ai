<?php
require_once ('../ebusclient/EntrustQuerySign.php');
//1、生成内转交易请求对象
$tRequest = new EntrustQuerySign();
$tRequest->request["SignNo"] = ($_POST['SignNo']); //签约编号

//2、传送内转交易并取得退货结果
$tResponse = $tRequest->postRequest();

//3、内转交易请求提交成功，返回结果信息
if ($tResponse->isSuccess()) {
	print ("<br>Success!!!" . "</br>");
	print ("ReturnCode   = [" . $tResponse->getReturnCode() . "]</br>");
	print ("ReturnMsg   = [" . $tResponse->getErrorMessage() . "]</br>");
    print ("TrxType   = [" . $tResponse->GetValue("TrxType") . "]</br>");
    print ("MerchantNo   = [" . $tResponse->GetValue("MerchantNo") . "]</br>");
    print ("SignNo   = [" . $tResponse->GetValue("SignNo") . "]</br>");
    print ("AgentSignNo   = [" . $tResponse->GetValue("AgentSignNo") . "]</br>");
    print ("CertificateNo   = [" . $tResponse->GetValue("CertificateNo") . "]</br>");
    print ("CertificateType   = [" . $tResponse->GetValue("CertificateType") . "]</br>");
    print ("Last4CardNo   = [" . $tResponse->GetValue("Last4CardNo") . "]</br>");
    print ("SignDate   = [" . $tResponse->GetValue("SignDate") . "]</br>");
    print ("UnSignDate   = [" . $tResponse->GetValue("UnSignDate") . "]</br>");
    print ("InvaidDate   = [" . $tResponse->GetValue("InvaidDate") . "]</br>");
    print ("AgentSignStatus   = [" . $tResponse->GetValue("AgentSignStatus") . "]</br>");
    print ("AccountType   = [" . $tResponse->GetValue("AccountType") . "]</br>");
    print ("SubMerchantNo   = [" . $tResponse->GetValue("SubMerchantNo") . "]</br>");
    print ("SignChannel   = [" . $tResponse->GetValue("SignChannel") . "]</br>");
} else {
	print ("<br>Failed!!!" . "</br>");
	print ("ReturnCode   = [" . $tResponse->getReturnCode() . "]</br>");
	print ("ReturnMsg   = [" . $tResponse->getErrorMessage() . "]</br>");
}
?>