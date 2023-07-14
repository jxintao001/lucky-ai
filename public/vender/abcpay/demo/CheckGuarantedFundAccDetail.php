<?php
require_once ('../ebusclient/CheckGuarantedFundAccDetail.php');
//1、生成内转交易请求对象
$tRequest = new CheckGuarantedFundAccDetail();
$tRequest->request["AccDate"] = ($_POST['AccDate']); //会计日期
$tRequest->request["NumEntryRec"] = ($_POST['NumEntryRec']); //翻页查询分录序号
$tRequest->request["NumSeqDtal"] = ($_POST['NumSeqDtal']); //翻页查询明细顺序号
$tRequest->request["JrnNo"] = ($_POST['JrnNo']); //翻页查询日志号

//2、传送内转交易并取得退货结果
$tResponse = $tRequest->postRequest();

//3、内转交易请求提交成功，返回结果信息
if ($tResponse->isSuccess()) {
	print ("<br>Success!!!" . "</br>");
	print ("ReturnCode   = [" . $tResponse->getReturnCode() . "]</br>");
	print ("ReturnMsg   = [" . $tResponse->getErrorMessage() . "]</br>");
	print ("DetailTable   = [" . iconv('GB2312','utf-8',$tResponse->GetValue("DetailTable")) . "]</br>");
    print ("NumEntryRec   = [" . $tResponse->GetValue("NumEntryRec") . "]</br>");
    print ("NumSeqDtal   = [" . $tResponse->GetValue("NumSeqDtal") . "]</br>");
    print ("JrnNo   = [" . $tResponse->GetValue("JrnNo") . "]</br>");
} else {
	print ("<br>Failed!!!" . "</br>");
	print ("ReturnCode   = [" . $tResponse->getReturnCode() . "]</br>");
	print ("ReturnMsg   = [" . $tResponse->getErrorMessage() . "]</br>");
}
?>