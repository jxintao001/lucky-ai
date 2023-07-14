<?php
require_once ('../ebusclient/DownloadStatement.php');
//1、生成内转交易请求对象
$tRequest = new DownloadStatement();
$tRequest->request["StatementDate"] = ($_POST['StatementDate']); //对账单日期
$tRequest->request["StatementType"] = ($_POST['StatementType']); //对账单类型
$tRequest->request["Zip"] = ($_POST['Zip']); //压缩标识

//2、传送内转交易并取得退货结果
$tResponse = $tRequest->postRequest();

//3、内转交易请求提交成功，返回结果信息
if ($tResponse->isSuccess()) {
	print ("<br>Success!!!" . "</br>");
	print ("ReturnCode   = [" . $tResponse->getReturnCode() . "]</br>");
	print ("ReturnMsg   = [" . $tResponse->getErrorMessage() . "]</br>");
    print ("TrxType   = [" . $tResponse->GetValue("TrxType") . "]</br>");
    print ("MD5HashedStatement   = [" . $tResponse->GetValue("MD5HashedStatement") . "]</br>");
} else {
	print ("<br>Failed!!!" . "</br>");
	print ("ReturnCode   = [" . $tResponse->getReturnCode() . "]</br>");
	print ("ReturnMsg   = [" . $tResponse->getErrorMessage() . "]</br>");
}
?>