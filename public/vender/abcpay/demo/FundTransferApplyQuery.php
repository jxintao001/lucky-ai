<?php
require_once ('../ebusclient/FundTransferApplyQuery.php');
//1、生成内转交易请求对象
$tRequest = new FundTransferApplyQuery();
$tRequest->request["SerialNumber"] = ($_POST['SerialNumber']); //校培监管资金划拨返回的OrderNo

//2、传送内转交易并取得退货结果
$tResponse = $tRequest->postRequest();

//3、内转交易请求提交成功，返回结果信息
if ($tResponse->isSuccess()) {
	print ("<br>Success!!!" . "</br>");
	print ("ReturnCode   = [" . $tResponse->getReturnCode() . "]</br>");
	print ("ReturnMsg   = [" . $tResponse->getErrorMessage() . "]</br>");
    print ("ApprovalStatus   = [" . $tResponse->GetValue("ApprovalStatus") . "]<br/>");
} else {
	print ("<br>Failed!!!" . "</br>");
	print ("ReturnCode   = [" . $tResponse->getReturnCode() . "]</br>");
	print ("ReturnMsg   = [" . $tResponse->getErrorMessage() . "]</br>");
}
?>