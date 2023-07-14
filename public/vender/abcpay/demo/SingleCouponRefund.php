<?php
require_once ('../ebusclient/SingleCouponRefund.php');
//1、生成退款请求对象
$tRequest = new SingleCouponRefund();

$tRequest->request["OrderNumber"] = ($_POST['txtOrgOrderNo']); //原交易编号（必要信息）
$tRequest->request["RefundOrderNo"] = ($_POST['txtCouponRefundNo']); //交易编号（必要信息）
$tRequest->request["Sign"] = ($_POST['txtSign']); //交易编号（必要信息）




//3、传送退款请求并取得退货结果
$tResponse = $tRequest->postRequest();
//var_dump($tResponse);
//调试用

//4、支付请求提交成功，返回结果信息
if ($tResponse->isSuccess()) {
    print ("<br>Success!!!" . "</br>");
    print ("ReturnCode   = [" . $tResponse->getReturnCode() . "]</br>");
    print ("ReturnMsg   = [" . $tResponse->getErrorMessage() . "]</br>");
    print ("OrderNo   = [" . $tResponse->GetValue("OrderNumber") . "]<br/>");
    print ("CouponRefundNo   = [" . $tResponse->GetValue("RefundOrderNo") . "]<br/>");
} else {
    print ("<br>Failed!!!" . "</br>");
    print ("ReturnCode   = [" . $tResponse->getReturnCode() . "]</br>");
    print ("ReturnMsg   = [" . $tResponse->getErrorMessage() . "]</br>");
}
?>