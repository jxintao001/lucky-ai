<?php
require_once ('../ebusclient/QRPayCode.php');
//1、生成请求对象，并将订单明细加入订单中
$tRequest = new QRPayCode();
//2、设定订单属性
$tRequest->order["QRPayCode"] = ($_POST['txtQRPayCode']); //客户二维码
$tRequest->order["Pid"] = ($_POST['txtPid']); //客户PID
$tRequest->order["PayTypeID"] = ($_POST['txtPayTypeID']); //设定交易类型
$tRequest->order["OrderNo"] = ($_POST['txtPaymentRequestNo']); //设定订单编号
$tRequest->order["ExpiredDate"] = ($_POST['txtExpiredDate']); //设定订单保存时间
$tRequest->order["OrderAmount"] = ($_POST['txtPaymentRequestAmount']); //设定交易金额
$tRequest->order["Fee"] = ($_POST['txtFee']); //设定手续费金额
$tRequest->order["AccountNo"] = ($_POST['txtAccountNo']); //设定支付账户
$tRequest->order["CurrencyCode"] = ($_POST['txtCurrencyCode']); //设定交易币种
$tRequest->order["ReceiverAddress"] = ($_POST['txtReceiverAddress']); //收货地址
$tRequest->order["InstallmentMark"] = ($_POST['txtInstallmentMark']); //分期标识

if (strcmp($installmentMerk, "1") == 0 && strcmp($paytypeID, "DividedPay") == 0) {
    $tRequest->order["InstallmentCode"] = ($_POST['txtInstallmentCode']); //设定分期代码
    $tRequest->order["InstallmentNum"] = ($_POST['txtInstallmentNum']); //设定分期期数
}

$tRequest->order["BuyIP"] = ($_POST['txtBuyIP']); //IP
$tRequest->order["OrderDesc"] = ($_POST['txtOrderDesc']); //设定订单说明
$tRequest->order["OrderURL"] = ($_POST['txtOrderURL']); //设定订单地址
$tRequest->order["OrderDate"] = ($_POST['txtOrderDate']); //设定订单日期 （必要信息 - YYYY/MM/DD）
$tRequest->order["OrderTime"] = ($_POST['txtOrderTime']); //设定订单时间 （必要信息 - HH:MM:SS）
$tRequest->order["orderTimeoutDate"] = ($_POST['txtorderTimeoutDate']); //设定订单有效期
$tRequest->order["CommodityType"] = ($_POST['txtCommodityType']); //设置商品种类

$installmentMerk = $_POST['txtInstallmentMark'];
$paytypeID = $_POST['txtPayTypeID'];

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
$tRequest->request["PaymentType"] = ($_POST['txtPaymentType']); //设定支付类型
$tRequest->request["PaymentLinkType"] = ($_POST['txtPaymentLinkType']); //设定支付接入方式
if ($_POST['txtPaymentType'] === "6" && $_POST['txtPaymentLinkType'] === "2") {
    $tRequest->request["UnionPayLinkType"] = ($_POST['txtUnionPayLinkType']); //当支付类型为6，支付接入方式为2的条件满足时，需要设置银联跨行移动支付接入方式
}
$tRequest->request["ReceiveAccount"] = ($_POST['txtReceiveAccount']); //设定收款方账号
$tRequest->request["ReceiveAccName"] = ($_POST['txtReceiveAccName']); //设定收款方户名
$tRequest->request["NotifyType"] = ($_POST['txtNotifyType']); //设定通知方式
$tRequest->request["ResultNotifyURL"] = ($_POST['txtResultNotifyURL']); //设定通知URL地址
$tRequest->request["MerchantRemarks"] = ($_POST['txtMerchantRemarks']); //设定附言
$tRequest->request["ReceiveMark"] = ($_POST['txtReceiveMark']); //交易是否直接入二级商户账户
$tRequest->request["ReceiveMerchantType"] = ($_POST['txtReceiveMerchantType']); //设定收款方账户类型
$tRequest->request["IsBreakAccount"] = ($_POST['txtIsBreakAccount']); //设定交易是否分账
$tRequest->request["SplitAccTemplate"] = ($_POST['txtSplitAccTemplate']); //分账模版编号

//4、添加分账信息
$splitmerchantid_arr = $_REQUEST['txtSplitMerchantID'];
$splitamount_arr = $_REQUEST['txtSplitAmount'];
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
    $PaymentURL = $tResponse->GetValue("PaymentURL");
    print ("<br>PaymentURL=$PaymentURL" . "</br>");
    echo "<script language='javascript'>";
    echo "window.location.href='$PaymentURL'";
    echo "</script>";
} else {
    print ("<br>Failed!!!" . "</br>");
    print ("ReturnCode   = [" . $tResponse->getReturnCode() . "]</br>");
    print ("ReturnMsg   = [" . $tResponse->getErrorMessage() . "]</br>");
}
?>


