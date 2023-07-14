<?php
require_once ('../ebusclient/MigrateSubMerToOtherSubject.php');
//1、生成内转交易请求对象
$tRequest = new MigrateSubMerToOtherSubject();
$tRequest->request["FromMerchantNo"] = ($_POST['FromMerchantNo']); //原商户号
$tRequest->request["FromSubMerchantNo"] = ($_POST['FromSubMerchantNo']); //原子商户号
$tRequest->request["CustomToSubMerchantNo"] = ($_POST['CustomToSubMerchantNo']); //自定义商户号

//2、传送内转交易并取得退货结果
$tResponse = $tRequest->postRequest();

//3、内转交易请求提交成功，返回结果信息
if ($tResponse->isSuccess()) {
	print ("<br>Success!!!" . "</br>");
	print ("ReturnCode   = [" . $tResponse->getReturnCode() . "]</br>");
	print ("ReturnMsg   = [" . $tResponse->getErrorMessage() . "]</br>");
    print ("SubMerchantAccNo   = [" . $tResponse->GetValue("SubMerchantAccNo") . "]</br>");
} else {
	print ("<br>Failed!!!" . "</br>");
	print ("ReturnCode   = [" . $tResponse->getReturnCode() . "]</br>");
	print ("ReturnMsg   = [" . $tResponse->getErrorMessage() . "]</br>");
}
?>