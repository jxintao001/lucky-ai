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
//增加了引用类 add by chj@20190307
class_exists('IBatchSatus') or require (dirname(__FILE__) . '/core/IBatchSatus.php');

class EntrustBatchQuery extends TrxRequest {
	public $request = array (
		"TrxType" => IFunctionID :: TRX_TYPE_ENTRUST_BATCHQUERY,
		"BatchNo" => "",
		"BatchDate" => ""
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
		//1.校验批次号最大长度
		if (!DataVerifier :: isValidString($this->request["BatchNo"], ILength :: ORDERID_LEN))
			throw new TrxException(TrxException :: TRX_EXC_CODE_1101, TrxException :: TRX_EXC_MSG_1101, "批次号长度超过限制或为空");
		//2.校验批次日期合法性
		if (!DataVerifier :: isValidDate($this->request["BatchDate"]))
			throw new TrxException(TrxException :: TRX_EXC_CODE_1101, TrxException :: TRX_EXC_MSG_1101, "批次日期格式不正确");
	}
	/// <summary>
	///  取得核准状态的中文说明。
	///  
	///  @param aStatus
	///             批量状态代码
	///  @return $tStatusChinese 批量状态的中文说明。
	/// </summary>
	public function getBatchSatusChinese($aStatus) {
		$tStatusChinese = "";
		/*IBatchStatus改为IBatchSatus,因为core下有的是IBatchSatus.php,为了和老版本兼容改的这里 modified by chj@20190307*/
		if ($aStatus === (IBatchSatus :: STATUS_UNCHECK)) {
			$tStatusChinese = "批量待复核";
		} else
			if ($aStatus === (IBatchSatus :: STATUS_CHECKSUCCESS)) {
				$tStatusChinese = "批量复核通过待发送";
			} else
				if ($aStatus === (IBatchSatus :: STATUS_REJECT)) {
					$tStatusChinese = "批量复核被驳回";
				} else
					if ($aStatus === (IBatchSatus :: STATUS_SEND)) {
						$tStatusChinese = "批量等待处理";
					} else
						if ($aStatus === (IBatchSatus :: STATUS_SUCCESS)) {
							$tStatusChinese = "批量提交成功";
						} else
							if ($aStatus === (IBatchSatus :: STATUS_FAIL)) {
								$tStatusChinese = "批量提交失败";
							} else {
								$tStatusChinese = "未知状态";
							}
		return $tStatusChinese;
	}
}
?>