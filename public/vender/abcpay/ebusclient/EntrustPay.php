<?php
class_exists('TrxRequest') or require (dirname(__FILE__) . '/core/TrxRequest.php');
class_exists('Json') or require (dirname(__FILE__) . '/core/Json.php');
class_exists('IChannelType') or require (dirname(__FILE__) . '/core/IChannelType.php');
class_exists('IPaymentType') or require (dirname(__FILE__) . '/core/IPaymentType.php');
class_exists('INotifyType') or require (dirname(__FILE__) . '/core/INotifyType.php');
class_exists('DataVerifier') or require (dirname(__FILE__) . '/core/DataVerifier.php');
class_exists('ILength') or require (dirname(__FILE__) . '/core/ILength.php');
class_exists('IPayTypeID') or require (dirname(__FILE__) . '/core/IPayTypeID.php');
class_exists('IInstallmentmark') or require (dirname(__FILE__) . '/core/IInstallmentmark.php');
class_exists('ICommodityType') or require (dirname(__FILE__) . '/core/ICommodityType.php');
class EntrustPay extends TrxRequest {
    public $request = array (
        "TrxType" => IFunctionID :: TRX_TYPE_ENTRUST_PAY,
        "OrderDate" => "",
        "OrderTime" => "",
        "OrderNo" => "",
        "EntrustSignNo" => "",
        "CardNo" => "",
        "CurrencyCode" => "",
        "Amount" => "",
        "ReceiverAddress" => "",
        "PaymentLinkType" => "",
        "BuyIP" => "",
        "ReceiveAccount" => "",
        "ReceiveAccName" => "",
        "MerchantRemarks" => "",
        "IsBreakAccount" => "",
        "BusinessCode" => "",
        "SplitAccInfoItems" => "",
        "SplitAmount" => "",
        "SplitMerchantID" => ""
    );
    function __construct() {
    }

    protected function getRequestMessage() {
        Json :: arrayRecursive($this->request, "urlencode", false);
        $tMessage = json_encode($this->request);
        $tMessage = urldecode($tMessage);
        return $tMessage;
    }

    /// 支付请求信息是否合法
    protected function checkRequest() {
        if (empty($this->request["OrderDate"]))
            throw new TrxException(TrxException :: TRX_EXC_CODE_1101, TrxException :: TRX_EXC_MSG_1101, "交易日期未设定！");
        if (empty($this->request["OrderTime"]))
            throw new TrxException(TrxException :: TRX_EXC_CODE_1101, TrxException :: TRX_EXC_MSG_1101, "交易时间未设定！");
        if (empty($this->request["OrderNo"]))
            throw new TrxException(TrxException :: TRX_EXC_CODE_1101, TrxException :: TRX_EXC_MSG_1101, "订单号未设定！");
        if (empty($this->request["EntrustSignNo"]))
            throw new TrxException(TrxException :: TRX_EXC_CODE_1101, TrxException :: TRX_EXC_MSG_1101, "代收支付签约号未设定！");
        if (empty($this->request["CardNo"]))
            throw new TrxException(TrxException :: TRX_EXC_CODE_1101, TrxException :: TRX_EXC_MSG_1101, "签约卡号未设定！");
        if (empty($this->request["CurrencyCode"]))
            throw new TrxException(TrxException :: TRX_EXC_CODE_1101, TrxException :: TRX_EXC_MSG_1101, "交易币种未设定！");
        if (empty($this->request["Amount"]))
            throw new TrxException(TrxException :: TRX_EXC_CODE_1101, TrxException :: TRX_EXC_MSG_1101, "交易金额未设定！");
        if (empty($this->request["BusinessCode"]))
            throw new TrxException(TrxException :: TRX_EXC_CODE_1101, TrxException :: TRX_EXC_MSG_1101, "代收业务种类未设定！");
        if (empty($this->request["PaymentLinkType"]))
            throw new TrxException(TrxException :: TRX_EXC_CODE_1101, TrxException :: TRX_EXC_MSG_1101, "支付交易渠道未设定！");
    }
}
?>