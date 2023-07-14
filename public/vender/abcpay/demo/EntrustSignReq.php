<?php
require_once ('../ebusclient/EntrustSignReq.php');
//1、生成内转交易请求对象
$tRequest = new EntrustSignReq();
$tRequest->request["SignNo"] = ($_POST['SignNo']); //签约编号
$tRequest->request["BusinessCode"] = ($_POST['BusinessCode']); //代收业务种类
$tRequest->request["SignChannel"] = ($_POST['SignChannel']); //签约渠道
$tRequest->request["SubMerchantID"] = ($_POST['SubMerchantID']); //二级商户号
$tRequest->request["SinglePaymentLimit"] = ($_POST['SinglePaymentLimit']); //代收交易限额
$tRequest->request["InValidDate"] = ($_POST['InValidDate']); //签约有效期
$tRequest->request["PayUnit"] = ($_POST['PayUnit']); //代收扣款时间单位
$tRequest->request["PayStep"] = ($_POST['PayStep']); //代收扣款时间步长
$tRequest->request["PayFrequency"] = ($_POST['PayFrequency']); //代收扣款频次
$tRequest->request["CustomAccType"] = ($_POST['CustomAccType']); //签约账户类型
$tRequest->request["CustomAccNo"] = ($_POST['CustomAccNo']); //签约卡号
$tRequest->request["CustomPhone"] = ($_POST['CustomPhone']); //签约手机号
$tRequest->request["CustomName"] = ($_POST['CustomName']); //签约账户户名
$tRequest->request["CustomCertType"] = ($_POST['CustomCertType']); //客户证件类型
$tRequest->request["CustomCertNo"] = ($_POST['CustomCertNo']); //客户证件号
$tRequest->request["SignDesc"] = ($_POST['SignDesc']); //代收扣款时间描述

//2、传送内转交易并取得退货结果
$tResponse = $tRequest->postRequest();

//3、内转交易请求提交成功，返回结果信息
if ($tResponse->isSuccess()) {
	print ("<br>Success!!!" . "</br>");
	print ("ReturnCode   = [" . $tResponse->getReturnCode() . "]</br>");
	print ("ReturnMsg   = [" . $tResponse->getErrorMessage() . "]</br>");
} else {
	print ("<br>Failed!!!" . "</br>");
	print ("ReturnCode   = [" . $tResponse->getReturnCode() . "]</br>");
	print ("ReturnMsg   = [" . $tResponse->getErrorMessage() . "]</br>");
}
?>