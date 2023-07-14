<?php
require_once ('../ebusclient/QueryPlatformConfirm.php');

//1、生成担保确认查询请求对象
$tRequest = new QueryPlatformConfirm();

//2、设置请求值
$tRequest->request["OrderNo"] = ($_POST['OrderNo']); //订单号（必要信息）
$tRequest->request["OrignalOrderNo"] = ($_POST['OrignalOrderNo']); //原订单号
$tRequest->request["SubMerchantNo"] = ($_POST['SubMerchantNo']); //子商户号


//3.传送担保确认查询
$tResponse = $tRequest->postRequest();

//4.获取请求结果,判断对账单查询结果状态，进行后续操作
if ($tResponse->isSuccess()) {       //5、对账单查询成功，生成对账单对象
    print ("<br>Success!!!" . "</br>");
    print ("ReturnCode   = [" . $tResponse->getReturnCode() . "]</br>");
    print ("ReturnMsg   = [" . $tResponse->getErrorMessage() . "]</br>");
    print ("TrxType   = [" . $tResponse->GetValue("TrxType") . "]</br>");
    print ("ResultCode   = [" . $tResponse->GetValue("ResultCode") . "]</br>");
    print ("ResultMessage   = [" . $tResponse->GetValue("SeResultMessagettleDate") . "]</br>");
    print ("Status   = [" . $tResponse->GetValue("Status") . "]<br/>");
} else {                             //6、对账单查询失败
    print ("<br>Failed!!!" . "</br>");
    print ("ReturnCode   = [" . $tResponse->getReturnCode() . "]</br>");
    print ("ReturnMsg   = [" . $tResponse->getErrorMessage() . "]</br>");
    print ("Status   = [" . $tResponse->GetValue("Status") . "]<br/>");
}
?>