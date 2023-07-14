<?php
require_once ('../ebusclient/GetWeiXinAuthInfo.php');

//1、生成请求对象
$tRequest = new GetWeiXinAuthInfo();

//2、设置请求值
$tRequest->request["StoreId"] = ($_POST['StoreId']); //门店编号 （必要信息）
$tRequest->request["StoreName"] = ($_POST['StoreName']); //门店名称（必要信息）
$tRequest->request["DeviceId"] = ($_POST['DeviceId']); //终端设备编号 （必要信息）
$tRequest->request["Attach"] = ($_POST['Attach']); //附加字段
$tRequest->request["RawData"] = ($_POST['RawData']); //初始化数据 （必要信息）
$tRequest->request["SubAppId"] = ($_POST['SubAppId']); //子商户绑定的公众号/小程序appid
$tRequest->request["Now"] = ($_POST['Now']); //当前时间 （必要信息）
$tRequest->request["VersionNo"] = ($_POST['VersionNo']); //版本号 （必要信息）
$tRequest->request["SignType"] = ($_POST['SignType']); //签名类型 （必要信息）


//3.传送交易请求
$tResponse = $tRequest->postRequest();

//4.获取请求结果
if ($tResponse->isSuccess()) {
    print ("<br>Success!!!" . "</br>");
    print ("ReturnCode   = [" . $tResponse->getReturnCode() . "]</br>");
    print ("ReturnMsg   = [" . $tResponse->getErrorMessage() . "]</br>");
    print ("AuthInfo   = [" . $tResponse->GetValue("AuthInfo") . "]</br>");
    print ("ExpiresIn   = [" . $tResponse->GetValue("ExpiresIn") . "]</br>");

} else {
    print ("<br>Failed!!!" . "</br>");
    print ("ReturnCode   = [" . $tResponse->getReturnCode() . "]</br>");
    print ("ReturnMsg   = [" . $tResponse->getErrorMessage() . "]</br>");
}
?>