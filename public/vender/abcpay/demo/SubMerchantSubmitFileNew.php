<?php
require_once ('../ebusclient/SubMerchantSubmitFileNew.php');

//1、生成请求对象
$tRequest = new SubMerchantSubmitFileNew();

//2、设置请求值
$tRequest->request["SubMerNo"] = ($_POST['SubMerId']);
$tRequest->request["SubMerCertFile"] = $_POST['SubMerCertFile'];
$tRequest->request["Flag"] = ($_POST['Flag']);
$tRequest->request["DeviceId"] = ($_POST['DeviceId']);

//3.传送交易请求
$tResponse = $tRequest->postRequest();

//4.获取请求结果
if ($tResponse->isSuccess()) {
    print ("<br>Success!!!" . "</br>");
    print ("ReturnCode   = [" . $tResponse->getReturnCode() . "]</br>");
    print ("ReturnMsg   = [" . $tResponse->getErrorMessage() . "]</br>");
    print ("仍需上传 = [" . $tResponse->GetValue("MerchantID") . "]<br/>");
} else {
    print ("<br>Failed!!!" . "</br>");
    print ("ReturnCode   = [" . $tResponse->getReturnCode() . "]</br>");
    print ("ReturnMsg   = [" . $tResponse->getErrorMessage() . "]</br>");
}
?>