<?php

namespace App\Services;

use Illuminate\Support\Facades\Log;

class AbcpayService
{
    public function merchanPay($pay_data)
    {
        include_once public_path('vender/abcpay/ebusclient') . '/PaymentRequest.php';
        Log::info("=====================Abcpay 农行支付交易====================");
        $tRequest = new \PaymentRequest();
        $tRequest->order["PayTypeID"] = 'ImmediatePay'; //设定交易类型
        $tRequest->order["OrderDate"] = date('Y/m/d'); //设定订单日期 （必要信息 - YYYY/MM/DD）
        $tRequest->order["OrderTime"] = date('H:i:s'); //设定订单时间 （必要信息 - HH:MM:SS）
        $tRequest->order["orderTimeoutDate"] = date('YmdHis', strtotime('+1 day')); //设定订单有效期
        $tRequest->order["OrderNo"] = $pay_data['out_trade_no']; //设定订单编号 （必要信息）
        $tRequest->order["CurrencyCode"] = "156"; //设定交易币种
        $tRequest->order["OrderAmount"] = $pay_data['total_fee']; //设定交易金额
        //$tRequest->order["Fee"] = "0"; //设定手续费金额
        //$tRequest->order["AccountNo"] = ($_POST['AccountNo']); //设定支付账户
        //$tRequest->order["OrderDesc"] = ($_POST['OrderDesc']); //设定订单说明
        //$tRequest->order["OrderURL"] = ($_POST['OrderURL']); //设定订单地址
        //$tRequest->order["ReceiverAddress"] = ($_POST['ReceiverAddress']); //收货地址
        //$tRequest->order["InstallmentMark"] = ($_POST['InstallmentMark']); //分期标识
//        $installmentMerk = $_POST['InstallmentMark'];
//        $paytypeID = $_POST['PayTypeID'];
//        if (strcmp($installmentMerk, "1") == 0 && strcmp($paytypeID, "DividedPay") == 0) {
//            $tRequest->order["InstallmentCode"] = ($_POST['InstallmentCode']); //设定分期代码
//            $tRequest->order["InstallmentNum"] = ($_POST['InstallmentNum']); //设定分期期数
//        }

        $tRequest->order["CommodityType"] = "0202"; //设置商品种类
        $tRequest->order["BuyIP"] = $_SERVER["REMOTE_ADDR"]; //IP
        //$tRequest->order["ExpiredDate"] = ($_POST['ExpiredDate']); //设定订单保存时间

        //2、订单明细
        $orderitem = array();
//        $orderitem["SubMerName"] = "Yygyl"; //设定二级商户名称
//        $orderitem["SubMerId"] = "Yygyl"; //设定二级商户代码
//        $orderitem["SubMerMCC"] = "0000"; //设定二级商户MCC码
//        $orderitem["SubMerchantRemarks"] = "测试"; //二级商户备注项
//        $orderitem["ProductID"] = "IP000001"; //商品代码，预留字段
        $orderitem["ProductName"] = "云熠供应链商品"; //商品名称
//        $orderitem["UnitPrice"] = "1.00"; //商品总价
//        $orderitem["Qty"] = "1"; //商品数量
//        $orderitem["ProductRemarks"] = ""; //商品备注项
//        $orderitem["ProductType"] = "其他"; //商品类型
//        $orderitem["ProductDiscount"] = "0.9"; //商品折扣
//        $orderitem["ProductExpiredDate"] = "10"; //商品有效期
        $tRequest->orderitems[0] = $orderitem;

        //3、生成支付请求对象
        $tRequest->request["PaymentType"] = 'A'; //设定支付类型
        $tRequest->request["PaymentLinkType"] = '1'; //设定支付接入方式
//        if ($_POST['PaymentType'] === "6" && $_POST['PaymentLinkType'] === "2") {
//            $tRequest->request["UnionPayLinkType"] = ($_POST['UnionPayLinkType']); //当支付类型为6，支付接入方式为2的条件满足时，需要设置银联跨行移动支付接入方式
//        }
//        $tRequest->request["ReceiveAccount"] = ($_POST['ReceiveAccount']); //设定收款方账号
//        $tRequest->request["ReceiveAccName"] = ($_POST['ReceiveAccName']); //设定收款方户名
        $tRequest->request["NotifyType"] = '1'; // 支付结果通知方式，0：仅页面跳转通知 1：页面跳转通知和服务器通知
        $tRequest->request["ResultNotifyURL"] = "https://yygyl-api.hzyy.store/payment/abcpay/notify"; // 	商户接收支付结果通知地址，商户自己填写
//        $tRequest->request["MerchantRemarks"] = ($_POST['MerchantRemarks']); //设定附言
//        $tRequest->request["ReceiveMark"] = ($_POST['ReceiveMark']); //交易是否直接入二级商户账户
//        $tRequest->request["ReceiveMerchantType"] = ($_POST['ReceiveMerchantType']); //设定收款方账户类型
        $tRequest->request["IsBreakAccount"] = '0'; //设定交易是否分账
//        $tRequest->request["SplitAccTemplate"] = ($_POST['SplitAccTemplate']); //分账模版编号

        //4、添加分账信息
//        $splitmerchantid_arr = $_REQUEST['SplitMerchantID'];
//        $splitamount_arr = $_REQUEST['SplitAmount'];
//        $item = array();
//        for ($i = 0; $i < count($splitmerchantid_arr, COUNT_NORMAL); $i++) {
//            $item["SplitMerchantID"] = $splitmerchantid_arr[$i];
//            $item["SplitAmount"] = $splitamount_arr[$i];
//            $tRequest->splitaccinfos[$i] = $item;
//            $item = array();
//        }

        $tResponse = $tRequest->postRequest();
        //支持多商户配置
        //$tResponse = $tRequest->extendPostRequest(2);

        if ($tResponse->isSuccess()) {
//            print ("<br>Success!!!" . "</br>");
//            print ("ReturnCode   = [" . $tResponse->getReturnCode() . "]</br>");
//            print ("ReturnMsg   = [" . $tResponse->getErrorMessage() . "]</br>");
            //添加一码多扫字段
            //print ("OneQRForAll   = [" . $tResponse->GetValue("OneQRForAll") . "]</br>");
            $PaymentURL = $tResponse->GetValue("PaymentURL");
            //print ("<br>PaymentURL=$PaymentURL" . "</br>");
            //echo "<script language='javascript'>";
            //echo "window.location.href='$PaymentURL'";
            //echo "</script>";

            $oneQRForAll = $tResponse->GetValue("OneQRForAll");
            $oneQRForAllArr = explode('?token=', $oneQRForAll);
            // 拼接原文
            $tokenID = $oneQRForAllArr[1];
            $originalText = 'method=invokePayFromBrowser&tokenID=' . $tokenID;

            // 加密方法
            $encryptionKey = 'G9w0BAQEFAAOCAQ8AMIIBCgKCAQEAvM3';
            $encryptionIV = 'EkpTEA3FbZFGGq8Y';

            // 加密原文
            $encryptedText = openssl_encrypt($originalText, 'AES-256-CBC', $encryptionKey, 0, $encryptionIV);

            // 构建JSON字符串
            $jsonData = json_encode([
                'method' => 'invokePayFromBrowser',
                'param'  => $encryptedText,
            ]);

            // URL编码转码
            $encodedData = urlencode($jsonData);

            // 构建scheme
            $scheme = 'bankabc://' . $encodedData;

            // 构建完整链接
            $pkgName = 'com.android.bankabc';
            $iosScheme = $scheme;
            $androidScheme = $scheme;
            $link = 'https://a.app.qq.com/o/simple.jsp?pkgname=' . $pkgName . '&ios_scheme=' . urlencode($iosScheme) . '&android_scheme=' . urlencode($androidScheme);
            return ['paymentURL' => $link];
        } else {
            print ("<br>Failed!!!" . "</br>");
            print ("ReturnCode   = [" . $tResponse->getReturnCode() . "]</br>");
            print ("ReturnMsg   = [" . $tResponse->getErrorMessage() . "]</br>");
            exit();
        }
    }

    public function refund($refundData)
    {
        include_once public_path('vender/abcpay/ebusclient') . '/RefundRequest.php';
        //1、生成退款请求对象
        $tRequest = new \RefundRequest();
        $tRequest->request["OrderDate"] = date('Y/m/d'); //订单日期（必要信息）
        $tRequest->request["OrderTime"] = date('H:i:s'); //订单时间（必要信息）
//        $tRequest->request["MerRefundAccountNo"] = '156'; //商户退款账号
//        $tRequest->request["MerRefundAccountName"] = '156'; //商户退款名
        $tRequest->request["OrderNo"] = $refundData['out_trade_no']; //原交易编号（必要信息）
        $tRequest->request["NewOrderNo"] = $refundData['out_refund_no']; //交易编号（必要信息）
        $tRequest->request["CurrencyCode"] = '156'; //交易币种（必要信息）
        $tRequest->request["TrxAmount"] = $refundData['refund_fee']; //退货金额 （必要信息）
//        $tRequest->request["RefundType"] = $refundData['RefundType']; //退款类型
//        $tRequest->request["MerRefundAccountFlag"] = $refundData['MerRefundAccountFlag']; //退款账簿类型
//        $tRequest->request["MerchantRemarks"] = $refundData['MerchantRemarks']; //附言
//
//        //2、增加二级商户信息
//        $splitmerchantid_arr = $_REQUEST['SplitMerchantID'];
//        $splitamount_arr = $_REQUEST['SplitAmount'];
//        $item = array ();
//        for ($i = 0; $i < count($splitmerchantid_arr, COUNT_NORMAL); $i++)
//        {
//            $item["SplitMerchantID"]=$splitmerchantid_arr[$i];
//            $item["SplitAmount"]=$splitamount_arr[$i];
//            $tRequest->splitaccinfos[$i]=$item;
//            $item = array ();
//        }
//
//        //增加分账模板信息
//        $splitAcc_arr = $_REQUEST['txtSplitAcc'];
//        $splitAccamount_arr = $_REQUEST['txtSplitAccAmount'];
//        $item = array ();
//        for ($i = 0; $i < count($splitAcc_arr, COUNT_NORMAL); $i++)
//        {
//            $item["SplitAcc"]=$splitAcc_arr[$i];
//            $item["SplitAmount"]=$splitAccamount_arr[$i];
//            $tRequest->splitAccTemplateInfo[$i]=$item;
//            $item = array ();
//        }
        //3、传送退款请求并取得退货结果
        $tResponse = $tRequest->postRequest();
        Log::error('退款结果：' . serialize($tResponse));
        //4、支付请求提交成功，返回结果信息
        if ($tResponse->isSuccess()) {
            $resData['return_code'] = '0000';
            $resData['return_msg'] = '退款成功';
        } else {
            $resData['return_code'] = $tResponse->getReturnCode();
            $resData['return_msg'] = $tResponse->getErrorMessage();
        }
        $resData['return_data'] = serialize($tResponse);
        return $resData;
    }

    public function verify()
    {
        include_once public_path('vender/abcpay/ebusclient') . '/Result.php';
        $notifyData = request()->all();
        $tResult = new \Result();
        $tResponse = $tResult->init($notifyData['MSG']);
        if ($tResponse->isSuccess()) {
            return [
                'MerchantID' => $tResponse->GetValue("MerchantID"),
                'TrxType'    => $tResponse->GetValue("TrxType"),
                'OrderNo'    => $tResponse->GetValue("OrderNo"),
                'Amount'     => $tResponse->GetValue("Amount"),
                'BatchNo'    => $tResponse->GetValue("BatchNo"),
                'VoucherNo'  => $tResponse->GetValue("VoucherNo"),
                'HostDate'   => $tResponse->GetValue("HostDate"),
                'HostTime'   => $tResponse->GetValue("HostTime"),
                'PayType'    => $tResponse->GetValue("PayType"),
                'NotifyType' => $tResponse->GetValue("NotifyType"),
                'iRspRef'    => $tResponse->GetValue("iRspRef"),
                'AccDate'    => $tResponse->GetValue("AccDate"),
                'AcqFee'     => $tResponse->GetValue("AcqFee"),
                'IssFee'     => $tResponse->GetValue("IssFee"),
                'JrnNo'      => $tResponse->GetValue("JrnNo"),
            ];
        } else {
            // 记录错误日志
            log::error("支付回调签名验证失败: " . $tResponse->getErrorMessage());
            // 抛出异常
            throw new \Exception('支付回调签名验证失败: ' . $tResponse->getErrorMessage());
        }
    }

    public function queryTrnxRecords($request)
    {
        include_once public_path('vender/abcpay/ebusclient') . '/QueryTrnxRecords.php';
        //1、、生成交易流水查询请求对象
        $tRequest = new \QueryTrnxRecords();
        $tRequest->request["SettleDate"] = $request->SettleDate; //查询日期YYYY/MM/DD （必要信息）
        $tRequest->request["SettleStartHour"] = $request->SettleStartHour; //查询开始时间段（0-23）
        $tRequest->request["SettleEndHour"] = $request->SettleEndHour; //查询截止时间段（0-23）
        $tRequest->request["ZIP"] = $request->ZIP ?? '0'; //是否压缩返回结果（0-不压缩，1-压缩）
        //2、传送交易流水查询请求并取得交易流水
        $tResponse = $tRequest->postRequest();
        //3、判断交易流水查询结果状态，进行后续操作
        if ($tResponse->isSuccess()) {
            return [
                'ReturnCode'        => '0000',
                'ReturnMsg'         => '查询成功',
                'TrxType'           => $tResponse->GetValue('TrxType'),
                'NumOfPayments'     => $tResponse->GetValue('NumOfPayments'),
                'SumOfPayAmount'    => $tResponse->GetValue('SumOfPayAmount'),
                'NumOfRefunds'      => $tResponse->GetValue('NumOfRefunds'),
                'SumOfRefundAmount' => $tResponse->GetValue('SumOfRefundAmount'),
                'ZIPDetailRecords'  => $tResponse->GetValue("ZIPDetailRecords"),
                'DetailRecords'     => $tResponse->GetValue("DetailRecords"),
            ];
        } else {
            return [
                'ReturnCode' => $tResponse->getReturnCode(),
                'ReturnMsg'  => $tResponse->getErrorMessage(),
            ];
        }

    }

}
