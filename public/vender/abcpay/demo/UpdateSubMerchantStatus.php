<?php
require_once ('../ebusclient/UpdateSubMerStatus.php');

//1、生成请求对象
$tRequest = new UpdateSubMerStatus();

//2、设置请求值
$tRequest->request["UpdateFlag"] = ($_POST['txtUpdateFlag']);
$tRequest->request["SubMerId"] = ($_POST['txtSubMerchantNo']);

//3.传送查询
$tResponse = $tRequest->postRequest();

//4.获取请求结果,判断查询结果状态，进行后续操作
if ($tResponse->isSuccess()) {
    print ("<br>Success!!!" . "</br>");
    print ("ReturnCode   = [" . $tResponse->getReturnCode() . "]</br>");
    print ("ReturnMsg   = [" . $tResponse->getErrorMessage() . "]</br>");

} else {
    print ("<br>Failed!!!" . "</br>");
    print ("ReturnCode   = [" . $tResponse->getReturnCode() . "]</br>");
    print ("ReturnMsg   = [" . $tResponse->getErrorMessage() . "]</br>");

}
?>