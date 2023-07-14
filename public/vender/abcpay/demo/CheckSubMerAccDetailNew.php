<?php
require_once ('../ebusclient/CheckSubMerAccDetailNew.php');
//1、生成内转交易请求对象
$tRequest = new CheckSubMerAccDetailNew();
$tRequest->request["SubMerchantNo"] = ($_POST['SubMerchantNo']); //子商户号
$tRequest->request["AccDate"] = ($_POST['AccDate']); //会计日期
$tRequest->request["NumEntryRec"] = ($_POST['NumEntryRec']); //翻页查询页码
$tRequest->request["JrnNo"] = ($_POST['JrnNo']); //日志号

//2、传送内转交易并取得退货结果
$tResponse = $tRequest->postRequest();

//3、内转交易请求提交成功，返回结果信息
if ($tResponse->isSuccess()) {
	print ("<br>Success!!!" . "</br>");
	print ("ReturnCode   = [" . $tResponse->getReturnCode() . "]</br>");
	print ("ReturnMsg   = [" . $tResponse->getErrorMessage() . "]</br>");
    print ("DetailTable   = [" . iconv('GB2312','utf-8',$tResponse->GetValue("DetailTable")) . "]</br>");
    print ("NextPage   = [" . $tResponse->GetValue("NextPage") . "]</br>");
    print ("NumEntryRec   = [" . $tResponse->GetValue("NumEntryRec") . "]</br>");
    print ("JrnNo   = [" . $tResponse->GetValue("JrnNo") . "]</br>");
} else {
	print ("<br>Failed!!!" . "</br>");
	print ("ReturnCode   = [" . $tResponse->getReturnCode() . "]</br>");
	print ("ReturnMsg   = [" . $tResponse->getErrorMessage() . "]</br>");
}
?>