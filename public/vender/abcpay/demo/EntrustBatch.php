<?php
require_once ('../ebusclient/EntrustBatch.php');
//1、取得批量授权扣款需要的信息
$seqno_arr = $_REQUEST['SeqNo'];
$orderno_arr = $_REQUEST['OrderNo'];
$entrustsignno_arr = $_REQUEST['EntrustSignNo'];
$orderamount_arr = $_REQUEST['OrderAmount'];
$businesscode_arr = $_REQUEST['BusinessCode'];
$buyip_arr = $_REQUEST['BuyIP'];
$receiveraddress_arr = $_REQUEST['ReceiverAddress'];
$cardno_arr = $_REQUEST['CardNo'];
$remark_arr = $_REQUEST['Remark'];

//2、生成批量授权扣款请求对象
$tRequest = new EntrustBatch();
$tRequest->agentBatch["BatchNo"] = ($_POST['BatchNo']);
$tRequest->agentBatch["BatchDate"] = ($_POST['BatchDate']);
$tRequest->agentBatch["BatchTime"] = ($_POST['BatchTime']);
$tRequest->agentBatch["TotalCount"] = ($_POST['TotalCount']);
$tRequest->agentBatch["TotalAmount"] = ($_POST['TotalAmount']);


//3、生成每个批次包明细
//取得列表项 
$item = array ();
for ($i = 0; $i < count($orderno_arr, COUNT_NORMAL); $i++) {
	$item["SeqNo"] = $seqno_arr[$i];
	$item["OrderNo"] = $orderno_arr[$i];
	$item["EntrustSignNo"] = $entrustsignno_arr[$i];
	$item["OrderAmount"] = $orderamount_arr[$i];
	$item["BusinessCode"] = $businesscode_arr[$i];
	$item["BuyIP"] = $buyip_arr[$i];
	$item["ReceiverAddress"] = $receiveraddress_arr[$i];
	$item["CardNo"] = $cardno_arr[$i];
	$item["Remark"] = $remark_arr[$i];
	$tRequest->details[$i] = $item;
	$item = array ();

	$tRequest->iSumAmount += $orderamount_arr[$i];
}

//4.传送交易请求
$tResponse = $tRequest->postRequest();

if ($tResponse->isSuccess()) {
	print ("<br>Success!!!" . "</br>");
	print ("ReturnCode   = [" . $tResponse->getReturnCode() . "]</br>");
	print ("ReturnMsg   = [" . $tResponse->getErrorMessage() . "]</br>");
	print ("TrxType   = [" . $tResponse->GetValue("TrxType") . "]<br/>");
	print ("MerchantNo = [" . $tResponse->GetValue("MerchantNo") . "]<br/>");
	print ("SendTime   = [" . $tResponse->GetValue("SendTime") . "]<br/>");
} else {
	print ("<br>Failed!!!" . "</br>");
	print ("ReturnCode   = [" . $tResponse->getReturnCode() . "]</br>");
	print ("ReturnMsg   = [" . $tResponse->getErrorMessage() . "]</br>");
}
?>