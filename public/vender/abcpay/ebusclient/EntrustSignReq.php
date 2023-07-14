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
class EntrustSignReq extends TrxRequest {
    public $request = array (
        "TrxType" => IFunctionID :: TRX_TYPE_ENTRUSTSIGNREQ,
        "SignNo" => "",
        "BusinessCode" => "",
        "SignChannel" => "",
        "SubMerchantID" => "",
        "SinglePaymentLimit" => "",
        "InValidDate" => "",
        "PayUnit" => "",
        "PayStep" => "",
        "PayFrequency" => "",
        "CustomAccType" => "",
        "CustomAccNo" => "",
        "CustomPhone" => "",
        "CustomName" => "",
        "CustomCertType" => "",
        "CustomCertNo" => "",
        "SignDesc" => ""
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
        if (empty($this->request["SignNo"]))
            throw new TrxException(TrxException :: TRX_EXC_CODE_1101, TrxException :: TRX_EXC_MSG_1101, "签约编号未设定！");
        if (empty($this->request["BusinessCode"]))
            throw new TrxException(TrxException :: TRX_EXC_CODE_1101, TrxException :: TRX_EXC_MSG_1101, "代收业务种类未设定！");
        if (empty($this->request["SignChannel"]))
            throw new TrxException(TrxException :: TRX_EXC_CODE_1101, TrxException :: TRX_EXC_MSG_1101, "签约渠道未设定！");
        if (empty($this->request["InValidDate"]))
            throw new TrxException(TrxException :: TRX_EXC_CODE_1101, TrxException :: TRX_EXC_MSG_1101, "签约有效期未设定！");
        if (empty($this->request["PayUnit"]))
            throw new TrxException(TrxException :: TRX_EXC_CODE_1101, TrxException :: TRX_EXC_MSG_1101, "代收扣款时间单位未设定！");
        if (empty($this->request["PayStep"]))
            throw new TrxException(TrxException :: TRX_EXC_CODE_1101, TrxException :: TRX_EXC_MSG_1101, "代收扣款时间步长未设定！");
        if (empty($this->request["CustomAccType"]))
            throw new TrxException(TrxException :: TRX_EXC_CODE_1101, TrxException :: TRX_EXC_MSG_1101, "签约账户类型未设定！");
		if (empty($this->request["CustomAccNo"]))
            throw new TrxException(TrxException :: TRX_EXC_CODE_1101, TrxException :: TRX_EXC_MSG_1101, "签约卡号未设定！");
		if (empty($this->request["CustomPhone"]))
            throw new TrxException(TrxException :: TRX_EXC_CODE_1101, TrxException :: TRX_EXC_MSG_1101, "签约手机号未设定！");
		if (empty($this->request["CustomName"]))
            throw new TrxException(TrxException :: TRX_EXC_CODE_1101, TrxException :: TRX_EXC_MSG_1101, "签约账户户名未设定！");
		if (empty($this->request["CustomCertType"]))
            throw new TrxException(TrxException :: TRX_EXC_CODE_1101, TrxException :: TRX_EXC_MSG_1101, "客户证件类型未设定！");
		if (empty($this->request["CustomCertNo"]))
            throw new TrxException(TrxException :: TRX_EXC_CODE_1101, TrxException :: TRX_EXC_MSG_1101, "客户证件号未设定！");
    }
}
?>