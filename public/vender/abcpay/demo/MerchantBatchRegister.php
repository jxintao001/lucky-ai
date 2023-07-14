<?php
require_once ('../ebusclient/BatchRegisterRequest.php');
//1、取得批量登记第三方订单需要的信息
$seqno_arr = $_REQUEST['SeqNo'];
$subMerchantNo_arr = $_REQUEST['SubMerchantNo'];
$subMerchantName_arr = $_REQUEST['SubMerchantName'];
$subAm_arr = $_REQUEST['SubAmt'];


//2、生成批量登记请求对象
$tRequest = new BatchRegisterRequest();
$tRequest->request["RequestNo"] = ($_POST['RequestNo']);
$tRequest->request["OrderNo"] = ($_POST['OrderNo']);
$tRequest->request["OrderDate"] = ($_POST['OrderDate']);
$tRequest->request["OrderAmount"] = ($_POST['OrderAmount']);
$tRequest->request["TrnxCode"] = ($_POST['TrnxCode']);
$tRequest->request["TrnxType"] = ($_POST['TrnxType']);
$tRequest->request["CurrencyCode"] = ($_POST['CurrencyCode']);
$tRequest->request["ProductName"] = ($_POST['ProductName']);
$tRequest->request["Remark"] = ($_POST['Remark']);



//3、生成每个批次包明细
//取得列表项
$item = array ();
for ($i = 0; $i < count($seqno_arr, COUNT_NORMAL); $i++) {
    $item["SeqNo"] = $seqno_arr[$i];

    $item["SubMerchantNo"] = $subMerchantNo_arr[$i];
    $item["SubMerchantName"] = $subMerchantName_arr[$i];
    $item["SubAmt"] = $subAm_arr[$i];
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
    print ("TotalCount = [" . $tResponse->GetValue("TotalCount") . "]<br/>");

    print ("TotalAmount   = [" . $tResponse->GetValue("TotalAmount") . "]<br/>");
    print ("SerialNumber   = [" . $tResponse->GetValue("SerialNumber") . "]<br/>");
    print ("HostDate = [" . $tResponse->GetValue("HostDate") . "]<br/>");
    print ("HostTime = [" . $tResponse->GetValue("HostTime") . "]<br/>");
} else {
    print ("<br>Failed!!!" . "</br>");
    print ("ReturnCode   = [" . $tResponse->getReturnCode() . "]</br>");
    print ("ReturnMsg   = [" . $tResponse->getErrorMessage() . "]</br>");
}