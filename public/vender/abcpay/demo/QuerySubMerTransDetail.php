<?php
require_once ('../ebusclient/QuerySubMerTransDetail.php');
//1、生成内转交易请求对象
$tRequest = new QuerySubMerTransDetail();
$tRequest->request["SubMerchantNo"] = ($_POST['SubMerchantNo']); //二级商户号
$tRequest->request["selType"] = ($_POST['selType']); //交易类型
$tRequest->request["Status"] = ($_POST['Status']); //交易状态
$tRequest->request["sDate"] = ($_POST['sDate']); //开始日期
$tRequest->request["eDate"] = ($_POST['eDate']); //结束日期
$tRequest->request["OrderNo"] = ($_POST['OrderNo']); //订单号

//2、传送内转交易并取得退货结果
$tResponse = $tRequest->postRequest();

//3、内转交易请求提交成功，返回结果信息
if ($tResponse->isSuccess()) {
	print ("<br>Success!!!" . "</br>");
	print ("ReturnCode   = [" . $tResponse->getReturnCode() . "]</br>");
	print ("ReturnMsg   = [" . $tResponse->getErrorMessage() . "]</br>");
    print ("TrxType   = [" . $tResponse->GetValue("TrxType") . "]</br>");
    print ("TrxList   = [" . $tResponse->GetValue("TrxList") . "]</br>");
} else {
	print ("<br>Failed!!!" . "</br>");
	print ("ReturnCode   = [" . $tResponse->getReturnCode() . "]</br>");
	print ("ReturnMsg   = [" . $tResponse->getErrorMessage() . "]</br>");
}
?>