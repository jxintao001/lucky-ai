<?php
require_once ('../ebusclient/SubMerchantSubFile.php');

//1、生成请求对象
$tRequest = new SubMerchantSubFile();

//2、设置请求值
$name = $_POST['fileName'];
$filestring = $tRequest->ZipFileToBase64String($name);
$tRequest->request["SubMerCertFile"] = $filestring;

$tRequest->request["SubMerNo"] = ($_POST['txtSubMerId']);
$tRequest->request["Flag"] = ($_POST['flag']);

//3.传送交易请求
$tResponse = $tRequest->postRequest();

//4.获取请求结果
if ($tResponse->isSuccess()) {
    print ("<br>Success!!!" . "</br>");
    print ("ReturnCode   = [" . $tResponse->getReturnCode() . "]</br>");
    print ("ReturnMsg   = [" . $tResponse->getErrorMessage() . "]</br>");

    //输出结果信息
    print ("MerchantName   = [" . iconv('GB2312','utf-8',$tResponse->GetValue("MerchantName")) . "]</br>");  //主商户名称
    print ("SubMerId   = [" . iconv('GB2312','utf-8',$tResponse->GetValue("SubMerId")) . "]</br>");  //二级商户号
    print ("SubMerchantAccNo   = [" . iconv('GB2312','utf-8',$tResponse->GetValue("SubMerchantAccNo")) . "]</br>");  //二级商户账号
    print ("Balance   = [" . iconv('GB2312','utf-8',$tResponse->GetValue("Balance")) . "]</br>");  //二级商户余额
} else {
    print ("<br>Failed!!!" . "</br>");
    print ("ReturnCode   = [" . $tResponse->getReturnCode() . "]</br>");
    print ("ReturnMsg   = [" . $tResponse->getErrorMessage() . "]</br>");
}
?>