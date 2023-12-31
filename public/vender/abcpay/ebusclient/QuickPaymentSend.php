﻿<?php
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
class QuickPaymentSend extends TrxRequest {
	public $order = array (
		"OrderNo" => "",
		"CurrencyCode" => "",
		"OrderAmount" => "",
		"Fee" => "",
		"OrderDate" => "",
		"OrderTime" => "",

		
	);
	public $request = array (
		"TrxType" => IFunctionID :: TRX_TYPE_KPAY_REQ,
		"AccName" => "",
		"CertificateType" => "",
		"CertificateID" => "",
		"ExpDate" => "",
		"CVV2" => "",
		"VerifyCode" => "",
		"PaymentType" => "",
		"PaymentLinkType" => "",
		"MerchantRemarks" => "",

		
	);
	function __construct() {
	}

	protected function getRequestMessage() {
		Json :: arrayRecursive($this->order, "urlencode", false);
		Json :: arrayRecursive($this->request, "urlencode", false);
		$js = '"Order":' . (json_encode(($this->order)));
		$js = substr($js, 0, -1) . '}}';
		$tMessage = json_encode($this->request);
		$tMessage = substr($tMessage, 0, -1);
		$tMessage = $tMessage . ',' . $js;
		$tMessage = urldecode($tMessage);
		return $tMessage;
	}

	/// 支付请求信息是否合法
	protected function checkRequest() {
		if (count($this->order) === 0)
			throw new TrxException(TrxException :: TRX_EXC_CODE_1100, TrxException :: TRX_EXC_MSG_1100, "未设定订单信息！");
		if (!DataVerifier :: isValidString($this->order["OrderNo"], ILength :: ORDERID_LEN))
			throw new TrxException(TrxException :: TRX_EXC_CODE_1100, TrxException :: TRX_EXC_MSG_1100, "交易编号为空！");
		if ($this->order["CurrencyCode"] !== "156")
			throw new TrxException(TrxException :: TRX_EXC_CODE_1101, TrxException :: TRX_EXC_MSG_1101, "交易币种不合法！");
		if (!DataVerifier :: isValidAmount($this->order["OrderAmount"], 2))
			throw new TrxException(TrxException :: TRX_EXC_CODE_1100, TrxException :: TRX_EXC_MSG_1100, "订单金额不合法");
		if (!DataVerifier :: isValidDate($this->order["OrderDate"]))
			throw new TrxException(TrxException :: TRX_EXC_CODE_1100, TrxException :: TRX_EXC_MSG_1100, "订单日期不合法");
		if (!DataVerifier :: isValidTime($this->order["OrderTime"]))
			throw new TrxException(TrxException :: TRX_EXC_CODE_1100, TrxException :: TRX_EXC_MSG_1100, "订单时间不合法");
			
		/* 和java保持一致，增加支付类型、支付渠道、手续费金额、贷记卡有效期、ccv2的判断
		   add by chj@20190311 */
		if (empty($this->request["PaymentType"]))
		  throw new TrxException(TrxException :: TRX_EXC_CODE_1100, TrxException :: TRX_EXC_MSG_1100, "未设定支付类型！");
		if (!($this->request["PaymentLinkType"] === IChannelType::PAY_LINK_TYPE_NET) && !($this->request["PaymentLinkType"] === IChannelType::PAY_LINK_TYPE_MOBILE) )
		  throw new TrxException(TrxException :: TRX_EXC_CODE_1100, TrxException :: TRX_EXC_MSG_1100, "支付渠道不合法");
		if(!empty($this->order["Fee"]))
		{
			if (!DataVerifier :: isValidAmount($this->order["Fee"], 2))
			  throw new TrxException(TrxException :: TRX_EXC_CODE_1100, TrxException :: TRX_EXC_MSG_1100, "手续费金额不合法");
		}
		if(!empty($this->request["ExpDate"]))
		{
		  if (!DataVerifier :: isValid($this->request["ExpDate"]))
		    throw new TrxException(TrxException :: TRX_EXC_CODE_1100, TrxException :: TRX_EXC_MSG_1100, "贷记卡有效期不合法");
		  if (strlen($this->request["ExpDate"])!=4)
		    throw new TrxException(TrxException :: TRX_EXC_CODE_1100, TrxException :: TRX_EXC_MSG_1100, "贷记卡有效期长度不合法");
		}
	  if(!empty($this->request["CVV2"]))
		{
		  if (!DataVerifier :: isValid($this->request["CVV2"]))
		    throw new TrxException(TrxException :: TRX_EXC_CODE_1100, TrxException :: TRX_EXC_MSG_1100, "CCV2不合法");
		  if (strlen($this->request["CVV2"])!=3)
		    throw new TrxException(TrxException :: TRX_EXC_CODE_1100, TrxException :: TRX_EXC_MSG_1100, "CCV2长度不合法");
		}
		
	}
}
?>