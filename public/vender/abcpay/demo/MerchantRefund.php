<?php
require_once ('../ebusclient/RefundRequest.php');
//1、生成退款请求对象
$tRequest = new RefundRequest();
$tRequest->request["OrderDate"] = ($_POST['OrderDate']); //订单日期（必要信息）
$tRequest->request["OrderTime"] = ($_POST['OrderTime']); //订单时间（必要信息）
$tRequest->request["MerRefundAccountNo"] = ($_POST['MerRefundAccountNo']); //商户退款账号
$tRequest->request["MerRefundAccountName"] = ($_POST['MerRefundAccountName']); //商户退款名
$tRequest->request["OrderNo"] = ($_POST['OrderNo']); //原交易编号（必要信息）
$tRequest->request["NewOrderNo"] = ($_POST['NewOrderNo']); //交易编号（必要信息）
$tRequest->request["CurrencyCode"] = ($_POST['CurrencyCode']); //交易币种（必要信息）
$tRequest->request["TrxAmount"] = ($_POST['TrxAmount']); //退货金额 （必要信息）
$tRequest->request["RefundType"] = ($_POST['RefundType']); //退款类型
$tRequest->request["MerRefundAccountFlag"] = ($_POST['txtMerRefundAccountFlag']); //退款账簿类型
$tRequest->request["MerchantRemarks"] = ($_POST['MerchantRemarks']); //附言

//2、增加二级商户信息
$splitmerchantid_arr = $_REQUEST['SplitMerchantID'];
$splitamount_arr = $_REQUEST['SplitAmount'];
$item = array ();
for ($i = 0; $i < count($splitmerchantid_arr, COUNT_NORMAL); $i++) 
{
    $item["SplitMerchantID"]=$splitmerchantid_arr[$i];
    $item["SplitAmount"]=$splitamount_arr[$i];
    $tRequest->splitaccinfos[$i]=$item;
    $item = array ();
}

//增加分账模板信息
$splitAcc_arr = $_REQUEST['txtSplitAcc'];
$splitAccamount_arr = $_REQUEST['txtSplitAccAmount'];
$item = array ();
for ($i = 0; $i < count($splitAcc_arr, COUNT_NORMAL); $i++)
{
    $item["SplitAcc"]=$splitAcc_arr[$i];
    $item["SplitAmount"]=$splitAccamount_arr[$i];
    $tRequest->splitAccTemplateInfo[$i]=$item;
    $item = array ();
}


//3、传送退款请求并取得退货结果
$tResponse = $tRequest->postRequest();
//var_dump($tResponse);
//调试用

//4、支付请求提交成功，返回结果信息
if ($tResponse->isSuccess()) {
	print ("<br>Success!!!" . "</br>");
	print ("ReturnCode   = [" . $tResponse->getReturnCode() . "]</br>");
	print ("ReturnMsg   = [" . $tResponse->getErrorMessage() . "]</br>");
	print ("OrderNo   = [" . $tResponse->GetValue("OrderNo") . "]<br/>");
	print ("NewOrderNo   = [" . $tResponse->GetValue("NewOrderNo") . "]<br/>");
	print ("TrxAmount = [" . $tResponse->GetValue("TrxAmount") . "]<br/>");
	print ("BatchNo   = [" . $tResponse->GetValue("BatchNo") . "]<br/>");
	print ("VoucherNo = [" . $tResponse->GetValue("VoucherNo") . "]<br/>");
	print ("HostDate  = [" . $tResponse->GetValue("HostDate") . "]<br/>");
	print ("HostTime  = [" . $tResponse->GetValue("HostTime") . "]<br/>");
	print ("iRspRef  = [" . $tResponse->GetValue("iRspRef") . "]<br/>");
} else {
	print ("<br>Failed!!!" . "</br>");
	print ("ReturnCode   = [" . $tResponse->getReturnCode() . "]</br>");
	print ("ReturnMsg   = [" . $tResponse->getErrorMessage() . "]</br>");
}
?>