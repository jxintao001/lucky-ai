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
class GuanteePaySendMQRequest extends TrxRequest {
    public $request = array (
        "TrxType" => IFunctionID :: TRX_TYPE_PAY_GUANTEEPAY,
        "OrderNo" => "",
        "NewOrderNo" => "",
        "OrderAmount" => ""
    );
    public $splitaccinfos = array ();
    function __construct() {
    }

    protected function getRequestMessage() {
        Json :: arrayRecursive($this->request, "urlencode", false);

        $js = '"SplitAccInfoItems":[';
        $count = count($this->splitaccinfos, COUNT_NORMAL);
        for ($i = 0; $i < $count; $i++) {
            Json :: arrayRecursive($this->splitaccinfos[$i], "urlencode", false);
            $js = $js . json_encode($this->splitaccinfos[$i]);
            if ($i < $count -1) {
                $js = $js . ',';
            }
        }
        $js = $js . ']}';

        $tMessage = json_encode($this->request);
        $tMessage = substr($tMessage, 0, -1);
        $tMessage = $tMessage . ',' . $js;

        $tMessage = urldecode($tMessage);
        return $tMessage;
    }

    /// 支付请求信息是否合法
    protected function checkRequest() {
        if (!DataVerifier :: isValidString($this->request["OrderNo"], ILength :: ORDERID_LEN))
            throw new TrxException(TrxException :: TRX_EXC_CODE_1100, TrxException :: TRX_EXC_MSG_1100, "担保支付交易编号不合法！");
        if (!DataVerifier :: isValidString($this->request["NewOrderNo"], ILength :: ORDERID_LEN))
            throw new TrxException(TrxException :: TRX_EXC_CODE_1100, TrxException :: TRX_EXC_MSG_1100, "担保确认交易编号不合法！");
        if (!DataVerifier :: isValidAmount($this->request["OrderAmount"], 2))
            throw new TrxException(TrxException :: TRX_EXC_CODE_1101, TrxException :: TRX_EXC_MSG_1101, "交易金额不合法！");

    }
}
?>