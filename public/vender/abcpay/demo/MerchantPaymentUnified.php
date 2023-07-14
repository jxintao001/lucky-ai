<?php
require_once ('../ebusclient/UnifiedPaymentRequest.php');

 
$tRequest = new UnifiedPaymentRequest();
$tRequest->order["PayTypeID"] = ($_POST['PayTypeID']); //设定交易类型
$tRequest->order["OrderDate"] = ($_POST['OrderDate']); //设定订单日期 （必要信息 - YYYY/MM/DD）
$tRequest->order["OrderTime"] = ($_POST['OrderTime']); //设定订单时间 （必要信息 - HH:MM:SS）
$tRequest->order["orderTimeoutDate"] = ($_POST['orderTimeoutDate']); //设定订单有效期
$tRequest->order["OrderNo"] = ($_POST['OrderNo']); //设定订单编号
$tRequest->order["CurrencyCode"] = ($_POST['CurrencyCode']); //设定交易币种
$tRequest->order["OrderAmount"] = ($_POST['PaymentRequestAmount']); //设定交易金额
$tRequest->order["AccountNo"] = ($_POST['AccountNo']); //指定付款账户
$tRequest->order["OrderDesc"] = ($_POST['OrderDesc']); //设定订单说明
$tRequest->order["OpenID"] = ($_POST['OpenID']); //指定付款账户
$tRequest->order["ReceiverAddress"] = ($_POST['ReceiverAddress']); //收货地址
$tRequest->order["BuyIP"] = ($_POST['BuyIP']); //IP
$tRequest->order["ExpiredDate"] = ($_POST['ExpiredDate']); //设定订单保存时间
$tRequest->order["LimitPay"] = ($_POST['LimitPay']); //限制贷记卡
$tRequest->order["TerminalNo"] = ($_POST['TerminalNo']); //设备终端号

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
$tRequest->request["PaymentLinkType"] = ($_POST['PaymentLinkType']); //设定支付接入
//$tRequest->request["ReceiveAccount"] = ($_POST['ReceiveAccount']); //与后台确认，不用
//$tRequest->request["ReceiveAccName"] = ($_POST['ReceiveAccName']); //与后台确认，不用
$tRequest->request["NotifyType"] = ($_POST['NotifyType']); //设定通知方式
$tRequest->request["ResultNotifyURL"] = ($_POST['ResultNotifyURL']); //设定通知URL地址
$tRequest->request["MerchantRemarks"] = ($_POST['MerchantRemarks']); //设定附言
$tRequest->request["IsBreakAccount"] = ($_POST['IsBreakAccount']); //设定交易是否分账
$tRequest->request["SplitAccTemplate"] = ($_POST['txtSplitAccTemplate']); //设定分账模板
$tRequest->request["CommodityType"] = ($_POST['CommodityType']); //设定商品种类
$tRequest->request["MerModelFlag"] = ($_POST['MerModelFlag']); //是否为大商户模式
$tRequest->request["SubMerchantID"] = ($_POST['SubMerchantID']); //大商户模式的子商户号

//4、场景设定
$tRequest->h5sceneinfo["H5SceneType"] = ($_POST['H5SceneType']);
$tRequest->h5sceneinfo["H5SceneUrl"] = ($_POST['H5SceneUrl']); //场景URL  
$tRequest->h5sceneinfo["H5SceneName"] = ($_POST['H5SceneName']);//场景名称  

//5、二级商户信息
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

if ($tResponse->isSuccess()) 
{	
	$QRURL = $tResponse->GetValue("QRURL");
	$APP = $tResponse->GetValue("APP");
	$JSAPI = $tResponse->GetValue("JSAPI");
	print ("<br>Success!!!" . "</br>");
	print ("ReturnCode   = [" . $tResponse->getReturnCode() . "]</br>");
	print ("ReturnMsg    = [" . $tResponse->getErrorMessage() . "]</br>");
  print ("MerchantID   = [" . $tResponse->GetValue("MerchantID") . "]<br/>");
	print ("TrxType      = [" . $tResponse->GetValue("TrxType") . "]<br/>");
	print ("OrderNo      = [" . $tResponse->GetValue("OrderNo") . "]<br/>");
	print ("OrderAmount  = [" . $tResponse->GetValue("OrderAmount") . "]<br/>");
	
	print ("PaymentURL  = [" . $tResponse->GetValue("PaymentURL") . "]<br/>");
	print ("HostDate  = [" . $tResponse->GetValue("HostDate") . "]<br/>");
	print ("HostTime  = [" . $tResponse->GetValue("HostTime") . "]<br/>");
	print ("PrePayID  = [" . $tResponse->GetValue("PrePayID") . "]<br/>");
	print ("ThirdOrderNo  = [" . $tResponse->GetValue("ThirdOrderNo") . "]<br/>");
	
	if (!empty($QRURL))
	{
		print ("QRURL  = [" . $tResponse->GetValue("QRURL") . "]<br/>");
	}
	else if(!empty($APP))
	{
    print ("appid   = [" . $tResponse->GetValue("appid") . "]<br/>");
	  print ("partnerid      = [" . $tResponse->GetValue("partnerid") . "]<br/>");
	  print ("prepayid      = [" . $tResponse->GetValue("prepayid") . "]<br/>");
	  print ("package  = [" . $tResponse->GetValue("package") . "]<br/>");
	  print ("noncestr  = [" . $tResponse->GetValue("noncestr") . "]<br/>");
	  print ("timestamp  = [" . $tResponse->GetValue("timestamp") . "]<br/>");
	  print ("sign  = [" . $tResponse->GetValue("sign") . "]<br/>");
	}
	else if(empty($JSAPI))
	{
    print ("appid   = [" . $tResponse->GetValue("appid") . "]<br/>");
    print ("timestamp  = [" . $tResponse->GetValue("timestamp") . "]<br/>");
    print ("noncestr  = [" . $tResponse->GetValue("noncestr") . "]<br/>");
    print ("package  = [" . $tResponse->GetValue("package") . "]<br/>");
	  print ("signType      = [" . $tResponse->GetValue("signType") . "]<br/>");
	  print ("paySign      = [" . $tResponse->GetValue("paySign") . "]<br/>");
	}
} 
else 
{
	print ("<br>Failed!!!" . "</br>");
	print ("ReturnCode   = [" . $tResponse->getReturnCode() . "]</br>");
	print ("ReturnMsg   = [" . $tResponse->getErrorMessage() . "]</br>");
}
?>


