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
class GetWeiXinAuthInfo extends TrxRequest {
    public $request = array (
        "TrxType" => IFunctionID :: TRX_TYPE_GET_WEIXINAUTHINFO,
        "StoreId" => "",
        "StoreName" => "",
        "DeviceId" => "",
        "Attach" => "",
        "RawData" => "",
        "SubAppId" => "",
        "Now" => "",
        "VersionNo" => "",
        "SignType" => ""
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
        if (empty($this->request["StoreId"]))
            throw new TrxException(TrxException :: TRX_EXC_CODE_1101, TrxException :: TRX_EXC_MSG_1101, "门店编号未设定！");
        if (empty($this->request["StoreName"]))
            throw new TrxException(TrxException :: TRX_EXC_CODE_1101, TrxException :: TRX_EXC_MSG_1101, "门店名称未设定！");
        if (empty($this->request["DeviceId"]))
            throw new TrxException(TrxException :: TRX_EXC_CODE_1101, TrxException :: TRX_EXC_MSG_1101, "终端设备编号未设定！");
        if (empty($this->request["RawData"]))
            throw new TrxException(TrxException :: TRX_EXC_CODE_1101, TrxException :: TRX_EXC_MSG_1101, "初始化数据未设定！");
        if (empty($this->request["Now"]))
            throw new TrxException(TrxException :: TRX_EXC_CODE_1101, TrxException :: TRX_EXC_MSG_1101, "当前时间未设定！");
        if (empty($this->request["VersionNo"]))
            throw new TrxException(TrxException :: TRX_EXC_CODE_1101, TrxException :: TRX_EXC_MSG_1101, "版本号未设定！");
        if (empty($this->request["SignType"]))
            throw new TrxException(TrxException :: TRX_EXC_CODE_1101, TrxException :: TRX_EXC_MSG_1101, "签名类型未设定！");
    }
}
?>