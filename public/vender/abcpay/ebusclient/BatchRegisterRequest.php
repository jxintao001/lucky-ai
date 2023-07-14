<?php
class_exists('TrxRequest') or require (dirname(__FILE__) . '/core/TrxRequest.php');
class_exists('Json') or require (dirname(__FILE__) . '/core/Json.php');
class_exists('IChannelType') or require (dirname(__FILE__) . '/core/IChannelType.php');
class_exists('IPaymentType') or require (dirname(__FILE__) . '/core/IPaymentType.php');
class_exists('INotifyType') or require (dirname(__FILE__) . '/core/INotifyType.php');
class_exists('DataVerifier') or require (dirname(__FILE__) . '/core/DataVerifier.php');
class_exists('ILength') or require (dirname(__FILE__) . '/core/ILength.php');
class_exists('IPayTypeID') or require (dirname(__FILE__) . '/core/IPayTypeID.php');
class_exists('IIsBreakAccountType') or require (dirname(__FILE__) . '/core/IIsBreakAccountType.php');
class BatchRegisterRequest extends TrxRequest {
    public $details = array ();
    public $request = array (
        "TrxType" => IFunctionID :: TRX_TYPE_OVERDUEREGISTER,
        "RequestNo" => "",
        "OrderNo" => "",
        "OrderDate" => "",
        "OrderAmount" => "",
        "TrnxCode" => "",
        "TrnxType" => "",
        "CurrencyCode" => "",
        "CustomId" => "",
        "ProductName" => "",
        "Remark" => ""

    );
    public $iSumAmount = 0;
    function __construct() {
    }

    protected function getRequestMessage() {
        Json :: arrayRecursive($this->request, "urlencode", false);
        $js = '"OrderData":[';
        $count = count($this->details, COUNT_NORMAL);
        for ($i = 0; $i < $count; $i++) {
            Json :: arrayRecursive($this->details[$i], 'urlencode', false);
            $js = $js . json_encode($this->details[$i]);
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
        /*
        if ((int) $this->request["TotalCount"] !== count($this->details, COUNT_NORMAL)) {
            throw new TrxException(TrxException :: TRX_EXC_CODE_1101, TrxException :: TRX_EXC_MSG_1101, "批内明细合计笔数(" . count($this->details, COUNT_NORMAL) . ")与批次的总笔数(" . $this->request["TotalCount"] . ")不符");
        }
        if (count($this->details, COUNT_NORMAL) > ILength :: MAXSUMCOUNT) {
            throw new TrxException(TrxException :: TRX_EXC_CODE_1101, TrxException :: TRX_EXC_MSG_1101, "批次的总笔数(" . count($this->details, COUNT_NORMAL) . ")超过最大限制(" . ILength :: MAXSUMCOUNT . ")");
        }

        if (!DataVerifier :: isValidString($this->request["BatchNo"], ILength :: ORDERID_LEN))
            throw new TrxException(TrxException :: TRX_EXC_CODE_1101, TrxException :: TRX_EXC_MSG_1101, "批次号长度超过限制或为空");
        if (!DataVerifier :: isValidDate($this->request["BatchDate"]))
            throw new TrxException(TrxException :: TRX_EXC_CODE_1101, TrxException :: TRX_EXC_MSG_1101, "订单日期格式不正确");
        if (!DataVerifier :: isValidTime($this->request["BatchTime"]))
            throw new TrxException(TrxException :: TRX_EXC_CODE_1101, TrxException :: TRX_EXC_MSG_1101, "订单时间格式不正确");
        if (!DataVerifier :: isValidAmount($this->request["TotalAmount"], 2))
            throw new TrxException(TrxException :: TRX_EXC_CODE_1101, TrxException :: TRX_EXC_MSG_1101, "交易总金额不合法");
       */

        #endregion
        //验证dic信息
       foreach ($this->details as $detail) {
           /*
           if ($detail["CurrencyCode"] !== "156")
               throw new TrxException(TrxException :: TRX_EXC_CODE_1101, TrxException :: TRX_EXC_MSG_1101, "订单明细中序列号". $detail["SeqNo"] . "交易币种不合法");
            if (!DataVerifier :: isValid($detail["SeqNo"]))
                throw new TrxException(TrxException :: TRX_EXC_CODE_1100, TrxException :: TRX_EXC_MSG_1101, "订单明细中序列号不合法！");
           if (!DataVerifier :: isValidString($detail["SeqNo"], ILength :: MAXSUMCOUNT))
               throw new TrxException(TrxException :: TRX_EXC_CODE_1100, TrxException :: TRX_EXC_MSG_1101, "订单明细中序列号不合法！");
            if (!DataVerifier :: isValidString($detail["OrderNo"], ILength :: ORDERID_LEN))
                throw new TrxException(TrxException :: TRX_EXC_CODE_1100, TrxException :: TRX_EXC_MSG_1101, "订单明细中原交易编号不合法！");
            if (!DataVerifier :: isValidAmount($detail["OrderAmount"], 2))
                throw new TrxException(TrxException :: TRX_EXC_CODE_1100, TrxException :: TRX_EXC_MSG_1101, "订单明细中交易金额不合法！");
           if (!DataVerifier :: isValidString($detail["UnionPayOrderNo"], ILength :: ORDERID_LEN))
               throw new TrxException(TrxException :: TRX_EXC_CODE_1100, TrxException :: TRX_EXC_MSG_1101, "订单明细中交易编号不合法！");
           */

        }

    }
}
?>