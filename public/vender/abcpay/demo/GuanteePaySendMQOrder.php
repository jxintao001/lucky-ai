<?php
require_once ('../ebusclient/ThdOrderConfirm.php');
//1、生成订单对象，并将订单明细加入订单中
$tRequest = new ThdOrderConfirm();
$tRequest->request["OrderNo"] = ($_POST['txtOrderNo']); //担保支付交易编号（必要信息）
$tRequest->request["NewOrderNo"] = ($_POST['txtNewOrderNo']); //担保确认交易编号（必要信息）
$tRequest->request["OrderAmount"] = ($_POST['txtOrderAmount']); //交易金额（必要信息）
$tRequest->request["AdvancedFund"] = ($_POST['txtAdvancedFund']); //是否垫资


//2、添加分账信息
$splitmerchantid_arr = $_REQUEST['txtSplitMerchantID'];
$splitamount_arr = $_REQUEST['txtSplitAmount'];
$item = array ();
for ($i = 0; $i < count($splitmerchantid_arr, COUNT_NORMAL); $i++)
{
    $item["SplitMerchantID"]=$splitmerchantid_arr[$i];
    $item["SplitAmount"]=$splitamount_arr[$i];
    $tRequest->splitaccinfos[$i]=$item;
    $item = array ();
}

//3、传送支付请求并取得支付网址
$tResponse = $tRequest->postRequest();

//4、支付请求提交成功，返回结果信息
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