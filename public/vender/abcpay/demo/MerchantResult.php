<?php
require_once ('../ebusclient/Result.php');
//1、取得MSG参数，并利用此参数值生成验证结果对象
$tResult = new Result();
$tResponse = $tResult->init($_POST['MSG']);

if ($tResponse->isSuccess()) {
	//2、、支付成功
	print ("TrxType         = [" . $tResponse->getValue("TrxType") . "]<br/>");
	print ("OrderNo         = [" . $tResponse->getValue("OrderNo") . "]<br/>");
	print ("Amount          = [" . $tResponse->getValue("Amount") . "]<br/>");
	print ("BatchNo         = [" . $tResponse->getValue("BatchNo") . "]<br/>");
	print ("VoucherNo       = [" . $tResponse->getValue("VoucherNo") . "]<br/>");
	print ("HostDate        = [" . $tResponse->getValue("HostDate") . "]<br/>");
	print ("HostTime        = [" . $tResponse->getValue("HostTime") . "]<br/>");
	print ("MerchantRemarks = [" . $tResponse->getValue("MerchantRemarks") . "]<br/>");
	print ("PayType         = [" . $tResponse->getValue("PayType") . "]<br/>");
	print ("NotifyType      = [" . $tResponse->getValue("NotifyType") . "]<br/>");
	/*根据java版新增三个属性 add by chj@20190306*/
	print ("TrnxNo          = [" . $tResponse->getValue("iRspRef") . "]<br/>");
	print ("BankType        = [" . $tResponse->getValue("BankType") . "]<br/>");
	print ("ThirdOrderNo    = [" . $tResponse->getValue("ThirdOrderNo") . "]<br/>");

} else {
	//3、失败
	print ("<br>ReturnCode   = [" . $tResponse->getReturnCode() . "]<br>");
	print ("ErrorMessage = [" . $tResponse->getErrorMessage() . "]<br>");
}
?>