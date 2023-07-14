<?php
class_exists('TrxRequest') or require (dirname(__FILE__) . '/core/TrxRequest.php');
class_exists('Json') or require (dirname(__FILE__) . '/core/Json.php');
class_exists('IChannelType') or require (dirname(__FILE__) . '/core/IChannelType.php');
class_exists('IPaymentType') or require (dirname(__FILE__) . '/core/IPaymentType.php');
class_exists('INotifyType') or require (dirname(__FILE__) . '/core/INotifyType.php');
class_exists('DataVerifier') or require (dirname(__FILE__) . '/core/DataVerifier.php');


class SettleAdFundTransErrFile extends TrxRequest{

    public $request = array (
        "TrxType" => IFunctionID :: TRX_TYPE_JFERRORFILE_DOWNLOAD,
        "MerchantNo" => "",
        "SubMerchantNo" => "",
        "FileDate" => ""
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
        //合法性判断

        if (empty($this->request["MerchantNo"]))
            throw new TrxException(TrxException :: TRX_EXC_CODE_1100, TrxException :: TRX_EXC_MSG_1100, "未设定商E付商户号！");
        if (empty($this->request["FileDate"]))
            throw new TrxException(TrxException :: TRX_EXC_CODE_1100, TrxException :: TRX_EXC_MSG_1100, "未设定下载对账单日期！");
        if (!DataVerifier :: isValidDate($this->request["FileDate"]))
            throw new TrxException(TrxException :: TRX_EXC_CODE_1100, TrxException :: TRX_EXC_MSG_1100, "下载对账单日期不合法！");

    }


}