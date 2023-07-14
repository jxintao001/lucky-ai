<?php
require_once ('../ebusclient/EntrustPay.php');
//1、生成内转交易请求对象
$tRequest = new EntrustPay();
$tRequest->request["OrderDate"] = ($_POST['OrderDate']); //交易日期
$tRequest->request["OrderTime"] = ($_POST['OrderTime']); //交易时间
$tRequest->request["OrderNo"] = ($_POST['OrderNo']); //订单号
$tRequest->request["EntrustSignNo"] = ($_POST['EntrustSignNo']); //代收支付签约号
$tRequest->request["CardNo"] = ($_POST['CardNo']); //签约卡号
$tRequest->request["CurrencyCode"] = ($_POST['CurrencyCode']); //交易币种
$tRequest->request["Amount"] = ($_POST['Amount']); //交易金额
$tRequest->request["ReceiverAddress"] = ($_POST['ReceiverAddress']); //收货地址
$tRequest->request["PaymentLinkType"] = ($_POST['PaymentLinkType']); //支付交易渠道
$tRequest->request["BuyIP"] = ($_POST['BuyIP']); //客户IP
$tRequest->request["ReceiveAccount"] = ($_POST['ReceiveAccount']); //指定商户收款账户账号
$tRequest->request["ReceiveAccName"] = ($_POST['ReceiveAccName']); //指定商户收款账户户名
$tRequest->request["MerchantRemarks"] = ($_POST['MerchantRemarks']); //附言
$tRequest->request["IsBreakAccount"] = ($_POST['IsBreakAccount']); //交易是否支持向二级商户分账
$tRequest->request["BusinessCode"] = ($_POST['BusinessCode']); //代收业务种类
$tRequest->request["SplitAmount"] = ($_POST['SplitAmount']); //子商户分账金额
$tRequest->request["SplitMerchantID"] = ($_POST['SplitMerchantID']); //二级商户编号


//2、增加分账模板信息
$splitmerchantid_arr = $_REQUEST['SplitMerchantID'];
$splitamount_arr = $_REQUEST['SplitAmount'];
$item = array ();
for ($i = 0; $i < count($splitmerchantid_arr, COUNT_NORMAL); $i++) 
{
    $item["SplitMerchantID"]=$splitmerchantid_arr[$i];
    $item["SplitAmount"]=$splitamount_arr[$i];
    $tRequest->SplitAccInfoItems[$i]=$item;
    $item = array ();
}

//2、传送内转交易并取得退货结果
$tResponse = $tRequest->postRequest();

//3、内转交易请求提交成功，返回结果信息
if ($tResponse->isSuccess()) {
	print ("<br>Success!!!" . "</br>");
	print ("ReturnCode   = [" . $tResponse->getReturnCode() . "]</br>");
	print ("ReturnMsg   = [" . $tResponse->getErrorMessage() . "]</br>");
    print ("TrxType   = [" . $tResponse->GetValue("TrxType") . "]</br>");
    print ("OrderNo   = [" . $tResponse->GetValue("OrderNo") . "]</br>");
} else {
	print ("<br>Failed!!!" . "</br>");
	print ("ReturnCode   = [" . $tResponse->getReturnCode() . "]</br>");
	print ("ReturnMsg   = [" . $tResponse->getErrorMessage() . "]</br>");
}
?>