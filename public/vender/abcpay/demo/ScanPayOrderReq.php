<?php
require_once ('../ebusclient/ScanPayOrderReq.php');
 
$tRequest = new PaymentRequest();
$tRequest->order["PayTypeID"] = ($_POST['PayTypeID']); //设定交易类型
$tRequest->order["OrderDate"] = ($_POST['OrderDate']); //设定订单日期 （必要信息 - YYYY/MM/DD）
$tRequest->order["OrderTime"] = ($_POST['OrderTime']); //设定订单时间 （必要信息 - HH:MM:SS）
$tRequest->order["orderTimeoutDate"] = ($_POST['orderTimeoutDate']); //设定订单有效期
$tRequest->order["OrderNo"] = ($_POST['OrderNo']); //设定订单编号
$tRequest->order["CurrencyCode"] = ($_POST['CurrencyCode']); //设定交易币种
$tRequest->order["OrderAmount"] = ($_POST['OrderAmount']); //设定交易金额
$tRequest->order["Fee"] = ($_POST['Fee']); //设定手续费金额
$tRequest->order["AccountNo"] = ($_POST['AccountNo']); //设定支付账户
$tRequest->order["OrderDesc"] = ($_POST['OrderDesc']); //设定订单说明
$tRequest->order["OrderURL"] = ($_POST['OrderURL']); //设定订单地址
$tRequest->order["ReceiverAddress"] = ($_POST['ReceiverAddress']); //收货地址
$tRequest->order["InstallmentMark"] = ($_POST['InstallmentMark']); //分期标识
$installmentMerk = $_POST['InstallmentMark'];
$paytypeID = $_POST['PayTypeID'];
if (strcmp($installmentMerk, "1") == 0 && strcmp($paytypeID, "DividedPay") == 0) {
	$tRequest->order["InstallmentCode"] = ($_POST['InstallmentCode']); //设定分期代码
	$tRequest->order["InstallmentNum"] = ($_POST['InstallmentNum']); //设定分期期数
}

$tRequest->order["CommodityType"] = ($_POST['CommodityType']); //设置商品种类
$tRequest->order["BuyIP"] = ($_POST['BuyIP']); //IP
$tRequest->order["ExpiredDate"] = ($_POST['ExpiredDate']); //设定订单保存时间


//2、订单明细
$orderitem = array ();
$orderitem["SubMerName"] = "测试二级商户1"; //设定二级商户名称
$orderitem["SubMerId"] = "12345"; //设定二级商户代码
$orderitem["SubMerMCC"] = "0000"; //设定二级商户MCC码 
$orderitem["SubMerchantRemarks"] = "测试"; //二级商户备注项
$orderitem["ProductID"] = "IP000001"; //商品代码，预留字段
$orderitem["ProductName"] = "中国移动IP卡"; //商品名称
$orderitem["UnitPrice"] = "1.00"; //商品总价
$orderitem["Qty"] = "1"; //商品数量
$orderitem["ProductRemarks"] = "测试商品"; //商品备注项
$orderitem["ProductType"] = "充值类"; //商品类型
$orderitem["ProductDiscount"] = "0.9"; //商品折扣
$orderitem["ProductExpiredDate"] = "10"; //商品有效期
$tRequest->orderitems[0] = $orderitem;

$orderitem = array ();
$orderitem["SubMerName"] = "测试二级商户2"; //设定二级商户名称
$orderitem["SubMerId"] = "12345"; //设定二级商户代码
$orderitem["SubMerMCC"] = "0000"; //设定二级商户MCC码 
$orderitem["SubMerchantRemarks"] = "测试2"; //二级商户备注项
$orderitem["ProductID"] = "IP000001"; //商品代码，预留字段
$orderitem["ProductName"] = "中国移动IP卡2"; //商品名称
$orderitem["UnitPrice"] = "1.00"; //商品总价
$orderitem["Qty"] = "1"; //商品数量
$orderitem["ProductRemarks"] = "测试商品2"; //商品备注项
$orderitem["ProductType"] = "充值类2"; //商品类型
$orderitem["ProductDiscount"] = "0.9"; //商品折扣
$orderitem["ProductExpiredDate"] = "10"; //商品有效期
$tRequest->orderitems[1] = $orderitem;

//3、生成支付请求对象
$tRequest->request["PaymentType"] = ($_POST['PaymentType']); //设定支付类型
$tRequest->request["PaymentLinkType"] = ($_POST['PaymentLinkType']); //设定支付接入方式
if ($_POST['PaymentType'] === "6" && $_POST['PaymentLinkType'] === "2") {
	$tRequest->request["UnionPayLinkType"] = ($_POST['UnionPayLinkType']); //当支付类型为6，支付接入方式为2的条件满足时，需要设置银联跨行移动支付接入方式
}
$tRequest->request["ReceiveAccount"] = ($_POST['ReceiveAccount']); //设定收款方账号
$tRequest->request["ReceiveAccName"] = ($_POST['ReceiveAccName']); //设定收款方户名
$tRequest->request["NotifyType"] = ($_POST['NotifyType']); //设定通知方式
$tRequest->request["ResultNotifyURL"] = ($_POST['ResultNotifyURL']); //设定通知URL地址
$tRequest->request["MerchantRemarks"] = ($_POST['MerchantRemarks']); //设定附言
$tRequest->request["IsBreakAccount"] = ($_POST['IsBreakAccount']); //设定交易是否分账
$tRequest->request["SplitAccTemplate"] = ($_POST['SplitAccTemplate']); //分账模版编号        

//4、添加分账信息
$splitmerchantid_arr = $_REQUEST['SplitMerchantID'];
$splitamount_arr = $_REQUEST['SplitAmount'];
$item = array ();
for ($i = 0; $i < count($splitmerchantid_arr, COUNT_NORMAL); $i++) 
{
    $item["SplitMerchantID"]=$splitmerchantid_arr[$i];
    $item["SplitAmount"]=$splitamount_arr[$i];
    $tRequest->splitaccinfos[$i]=$item;
    $item = array ();
}  

$tResponse = $tRequest->postRequest();
//支持多商户配置
//$tResponse = $tRequest->extendPostRequest(2);

if ($tResponse->isSuccess()) {
	print ("<br>Success!!!" . "</br>");
	print ("ReturnCode   = [" . $tResponse->getReturnCode() . "]</br>");
	print ("ReturnMsg   = [" . $tResponse->getErrorMessage() . "]</br>");
    //添加一码多扫字段
    print ("OneQRForAll   = [" . $tResponse->GetValue("OneQRForAll") . "]</br>");
	$PaymentURL = $tResponse->GetValue("PaymentURL");
	print ("<br>PaymentURL=$PaymentURL" . "</br>");
	//echo "<script language='javascript'>";
	//echo "window.location.href='$PaymentURL'";
	//echo "</script>";
} else {
	print ("<br>Failed!!!" . "</br>");
	print ("ReturnCode   = [" . $tResponse->getReturnCode() . "]</br>");
	print ("ReturnMsg   = [" . $tResponse->getErrorMessage() . "]</br>");
}
?>


