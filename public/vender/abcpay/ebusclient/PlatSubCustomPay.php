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
class PlatSubCustomPay extends TrxRequest {
    public $request = array (
        "TrxType" => IFunctionID :: TRX_TYPE_PLAT_SUBCUSTOM_PAY,
        "OrderNo" => "",
        "PlatSubCustomNo" => "",
        "PlatSubMerchantNo" => "",
        "Amount" => "",
        "Remark" => ""
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
        if (empty($this->request["OrderNo"]))
            throw new TrxException(TrxException :: TRX_EXC_CODE_1101, TrxException :: TRX_EXC_MSG_1101, "交易流水号未设定！");
        if (empty($this->request["PlatSubCustomNo"]))
            throw new TrxException(TrxException :: TRX_EXC_CODE_1101, TrxException :: TRX_EXC_MSG_1101, "转出方二级客户编号未设定！");
        if (empty($this->request["PlatSubMerchantNo"]))
            throw new TrxException(TrxException :: TRX_EXC_CODE_1101, TrxException :: TRX_EXC_MSG_1101, "转入方二级商户编号未设定！");
        if (empty($this->request["Amount"]))
            throw new TrxException(TrxException :: TRX_EXC_CODE_1101, TrxException :: TRX_EXC_MSG_1101, "交易金额未设定！");
    }
}
?>