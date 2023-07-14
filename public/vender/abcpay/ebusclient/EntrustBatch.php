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
class_exists('IIsBreakAccountType') or require (dirname(__FILE__) . '/core/IIsBreakAccountType.php');
class EntrustBatch extends TrxRequest {
	public $agentBatch = array (
		"BatchNo" => "",
		"BatchDate" => "",
		"BatchTime" => "",
		"TotalCount" => "",
		"TotalAmount" => ""
	);
	public $details = array ();
	public $request = array (
		"TrxType" => IFunctionID :: TRX_TYPE_ENTRUST_BATCH,
		"ReceiveAccount" => "",
		"ReceiveAccName" => "",
		"CurrencyCode" => ""
	);
	public $iSumAmount = 0;
	function __construct() {
	}

	protected function getRequestMessage() {
		Json :: arrayRecursive($this->agentBatch, "urlencode", false);
		Json :: arrayRecursive($this->request, "urlencode", false);
		$js = '"EntrustBatch":' . (json_encode(($this->agentBatch)));
		$js = substr($js, 0, -1);
		$js = $js . '},"Details":[';
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
		if (count($this->agentBatch, COUNT_NORMAL) === 0)
			throw new TrxException(TrxException :: TRX_EXC_CODE_1101, TrxException :: TRX_EXC_MSG_1101, "批次信息不允许为空");
		if ((int) $this->agentBatch["TotalCount"] !== count($this->details, COUNT_NORMAL)) {
			throw new TrxException(TrxException :: TRX_EXC_CODE_1101, TrxException :: TRX_EXC_MSG_1101, "批内明细合计笔数(" . count($this->details, COUNT_NORMAL) . ")与批次的总笔数(" . $this->agentBatch["TotalCount"] . ")不符");
		}
		if (count($this->details, COUNT_NORMAL) > ILength :: MAXSUMCOUNT) {
			throw new TrxException(TrxException :: TRX_EXC_CODE_1101, TrxException :: TRX_EXC_MSG_1101, "批次的总笔数(" . count($this->details, COUNT_NORMAL) . ")超过最大限制(" . ILength :: MAXSUMCOUNT . ")");
		}

		if (!DataVerifier :: isValidString($this->agentBatch["BatchNo"], ILength :: ORDERID_LEN))
			throw new TrxException(TrxException :: TRX_EXC_CODE_1101, TrxException :: TRX_EXC_MSG_1101, "批次号长度超过限制或为空");
		if (!DataVerifier :: isValidDate($this->agentBatch["BatchDate"]))
			throw new TrxException(TrxException :: TRX_EXC_CODE_1101, TrxException :: TRX_EXC_MSG_1101, "批次日期格式不正确");
		if (!DataVerifier :: isValidTime($this->agentBatch["BatchTime"]))
			throw new TrxException(TrxException :: TRX_EXC_CODE_1101, TrxException :: TRX_EXC_MSG_1101, "批次时间格式不正确");
		if (!DataVerifier :: isValidAmount($this->agentBatch["TotalAmount"], 2))
			throw new TrxException(TrxException :: TRX_EXC_CODE_1101, TrxException :: TRX_EXC_MSG_1101, "批量授权扣款总金额不正确");

		//验证dic信息
		foreach ($this->details as $detail) {
			if (!DataVerifier :: isValid($detail["SeqNo"]))
				throw new TrxException(TrxException :: TRX_EXC_CODE_1100, TrxException :: TRX_EXC_MSG_1101, "序列号不合法");
			if (!DataVerifier :: isValidString($detail["OrderNo"], ILength :: ORDERID_LEN))
				throw new TrxException(TrxException :: TRX_EXC_CODE_1100, TrxException :: TRX_EXC_MSG_1101, "订单号不合法");
			if (!DataVerifier :: isValidString($detail["EntrustSignNo"], ILength :: ORDERID_LEN))
				throw new TrxException(TrxException :: TRX_EXC_CODE_1100, TrxException :: TRX_EXC_MSG_1101, "授权支付签约号不合法");
			if (!DataVerifier :: isValidAmount($detail["OrderAmount"], 2))
				throw new TrxException(TrxException :: TRX_EXC_CODE_1100, TrxException :: TRX_EXC_MSG_1101, "交易金额不合法");
			
		}
	}
}

?>