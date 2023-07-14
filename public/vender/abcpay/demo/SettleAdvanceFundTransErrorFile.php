<?php
require_once ('../ebusclient/SettleAdFundTransErrFile.php');

//1、生成请求对象
$tRequest = new SettleAdFundTransErrFile();

//2、设置请求值

$tRequest->request["MerchantNo"] = ($_POST['MerchantNo']);
$tRequest->request["FileDate"] = ($_POST['FileDate']);

//3.传送交易请求
$tResponse = $tRequest->postRequest();

//4.获取请求结果
if ($tResponse->isSuccess()) {
    print ("<br>Success!!!" . "</br>");
    print ("ReturnCode   = [" . $tResponse->getReturnCode() . "]</br>");
    print ("ReturnMsg   = [" . $tResponse->getErrorMessage() . "]</br>");

    //输出结果信息
    print ("TrxType   = [" . iconv('GB2312','utf-8',$tResponse->GetValue("TrxType")) . "]</br>");
    print ("FileDate   = [" . iconv('GB2312','utf-8',$tResponse->GetValue("FileDate")) . "]</br>");
    print ("DetailRecords   = [" . iconv('GB2312','utf-8',$tResponse->GetValue("DetailRecords")) . "]</br>");

} else {
    print ("<br>Failed!!!" . "</br>");
    print ("ReturnCode   = [" . $tResponse->getReturnCode() . "]</br>");
    print ("ReturnMsg   = [" . $tResponse->getErrorMessage() . "]</br>");
}
?>