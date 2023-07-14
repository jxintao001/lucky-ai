<?php
require_once ('../ebusclient/GetReceiptRequest.php');
//1、生成退款批量结果查询请求对象
$tRequest = new GetReceiptRequest();
$tRequest->request["SubMerchantNo"] = ($_POST['SubMerchantNo']); //二级商户号
$tRequest->request["OrderNo"] = ($_POST['OrderNo']); //订单号

//2、传送退款批量结果查询请求并取得结果
$tResponse = $tRequest->postRequest();
//3、支付请求提交成功，返回结果信息
if ($tResponse->isSuccess()) {
	print ("<br>Success!!!" . "</br>");
	print ("ReturnCode  = [" . $tResponse->getReturnCode() . "]</br>");
	print ("ReturnMsg   = [" . $tResponse->getErrorMessage() . "]</br>");
	$str = $tResponse->GetValue("ImageCode");
	print ("ImageCode  = [" . $str ."]</br>");
	
	//获取解压解码后的图片流
	$sImageStr= $tRequest->decompressFromBase64String($str);	
	//设置文件路径和文件名
	$sImageName="../" . $_POST['OrderNo'] .".bmp";
	print ("ImageName  = [" . $sImageName ."]</br>");
	//保存图片
	$r=file_put_contents($sImageName,$sImageStr);
	
} else {
	print ("<br>Failed!!!" . "</br>");
	print ("ReturnCode   = [" . $tResponse->getReturnCode() . "]</br>");
	print ("ReturnMsg   = [" . $tResponse->getErrorMessage() . "]</br>");
}
?>