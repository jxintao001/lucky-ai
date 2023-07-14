<?php
require_once ('../ebusclient/MerQueryFileRegister.php');

//1、生成请求对象
$tRequest = new MerQueryFileRegister();

//2、设置请求值
$tRequest->request["BatchDate"] = ($_POST['txtBatchDate']);
$tRequest->request["BatchNo"] = ($_POST['txtBatchNo']);

//3.传送交易请求
$tResponse = $tRequest->postRequest();

//4.获取请求结果
if ($tResponse->isSuccess()) {
    print ("<br>Success!!!" . "</br>");
    print ("ReturnCode   = [" . $tResponse->getReturnCode() . "]</br>");
    print ("ReturnMsg   = [" . $tResponse->getErrorMessage() . "]</br>");
    print ("DetailRecords   = [" . $tResponse->GetValue("DetailRecords") . "]</br>");

} else {
    print ("<br>Failed!!!" . "</br>");
    print ("ReturnCode   = [" . $tResponse->getReturnCode() . "]</br>");
    print ("ReturnMsg   = [" . $tResponse->getErrorMessage() . "]</br>");
}