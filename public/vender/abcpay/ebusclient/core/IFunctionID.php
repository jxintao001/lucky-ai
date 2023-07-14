<?php
interface IFunctionID
{
     /// <summary>
        ///  商户交易请求类型 - 直接支付请求
        /// </summary>
         const  TRX_TYPE_PAY_REQ = "PayReq";
				/// <summary>
        ///  商户交易请求类型 - 综合收银台支付请求
        /// </summary>
        const TRX_TYPE_PAY_REQ_UNIFIED = "UnifiedOrderReq";
        /// <summary>
        ///  商户交易请求类型 - 预授权支付请求
        /// </summary>
         const  TRNX_TYPE_PAY_PREAUTH = "PreAuthReq";
        /// <summary>
        /// 商户交易请求类型 - 分期支付
        /// </summary>
         const  TRNX_TYPE_PAY_INSTALLMENT = "INSTALLMENTReq";

         /**
         * 商户交易请求类型 - 账单发送
         */
         const  TRX_TYPE_KPAYVERIFY_REQ = "KPayVerifyReq";
        /**
         * 商户交易请求类型 - 验证码重发
         */
         const  TRX_TYPE_KPAYRESEND_REQ = "KPayResendReq";
        /**
         * 商户交易请求类型 - K码支付
         */
         const  TRX_TYPE_KPAY_REQ = "KPayReq";
        /// <summary>
        ///  商户交易请求类型 - 手机支付请求
        /// </summary>
         const  TRX_TYPE_MOBILEPAY_REQ = "MobilePayReq";
        /// <summary>
        ///  商户交易请求类型 - 手机支付请求
        /// </summary>
         const  TRX_TYPE_MPAYREG_REQ = "MobilePayReg";
        /// <summary>
        ///  商户交易请求类型 - 卡余额查询请求
        /// </summary>
         const  TRX_TYPE_CRADBALANCE_REQ = "CardBalanceReq";
        /// <summary>
        ///  商户交易请求类型 - 取消支付
        /// </summary>
         const  TRX_TYPE_VOID_PAY = "VoidPay";

        /// <summary>
        ///  商户交易请求类型 - 退货
        /// </summary>
         const  TRX_TYPE_REFUND = "Refund";

        /// <summary>
        ///  商户交易请求类型 - 批量退款
        /// </summary>
         const  TRX_TYPE_OVERDUEREFUND = "BatchRefund";

        /// <summary>
        ///  商户交易请求类型 - 查询批量退款
        /// </summary>
         const  TRX_TYPE_QUERYOVERDUEREFUND = "QueryBatchRefund";

        /// <summary>
        ///  商户交易请求类型 - 取消退货
        /// </summary>
         const  TRX_TYPE_VOID_REFUND = "VoidRefund";

        /// <summary>
        ///  商户交易请求类型 - 对账
        /// </summary>
         const  TRX_TYPE_SETTLE = "Settle";

        /// <summary>
        ///  商户交易请求类型 - 支付宝微信对账 add by chj@20181203
        /// </summary>
         const  TRX_TYPE_SETTLEALIWX = "SettleAliWx";

        /// <summary>
        ///  商户交易请求类型 - 对账
        /// </summary>
         const  TRX_TYPE_CBPSETTLE = "CBPSettle";

        /// <summary>
        ///  商户交易请求类型 - 订单状态查询
        /// </summary>
         const  TRX_TYPE_QUERY = "Query";

        /// <summary>
        ///  商户交易请求类型 - 订单状态查询
        /// </summary>
         const  TRX_TYPE_QUERYTRNXRECORDS = "QueryTrnxRecords";

        /// <summary>
        ///  商户交易请求类型 - 支付结果通知
        /// </summary>
         const  TRX_TYPE_PAY_RESULT = "PayResult";


        /// <summary>
        ///  基金支付交易请求
        /// 
        /// </summary>
         const  TRX_TYPE_FUND_PAY_REQ = "FundPayReq";

        /// <summary>
        ///  身份验证交易请求
        /// 
        /// </summary>
         const  TRX_TYPE_CARD_VERIFY_REQ = "CardVerifyReq";

        /// <summary>
        ///  身份验证交易请求
        /// 
        /// </summary>
         const  TRX_TYPE_IDENTITY_VERIFY_REQ = "IdentityVerifyReq";
        /// <summary>
        ///  身份验证交易请求
        /// 
        /// </summary>
         const  TRX_TYPE_STATIC_IDENTITY_VERIFY_REQ = "StaticIdentifyVerifyReq";

        /// <summary>
        ///  退款批量文件发送
        /// 
        /// </summary>
         const  TRX_TYPE_BATCH_SEND_REQ = "RefundBatchSendReq";

        /// <summary>
        ///  查询批量处理结果
        /// 
        /// </summary>
         const  TRX_TYPE_QUERY_BATCH_REQ = "QueryBatchReq";

        /// <summary>
        ///  授权支付签约
        /// </summary>
         const  TRX_TYPE_EBUS_AgentSignContract_REQ = "AgentSign";
        /// <summary>
        ///  授权支付解约
        /// </summary>
         const  TRX_TYPE_EBUS_AgentUnsignContract_REQ = "AgentUnSign";
        /// <summary>
        ///  授权支付签约(商户端)
        /// </summary>
         const  TRX_TYPE_EBUS_InterfaceAgentSignContract_REQ = "InterfaceAgentSignReq";

        ///  授权支付签约短信验证码重发(商户端)
        /// </summary>
         const  TRX_TYPE_EBUS_InterfaceAgentSignContract_ReSend_REQ = "InterfaceAgentSignResend";

        ///  授权支付签约确认(商户端)
        /// </summary>
         const  TRX_TYPE_EBUS_InterfaceAgentSignSubmit_REQ = "InterfaceAgentSignSubmit";
        
        /// <summary>
        ///  授权支付单笔扣款
        /// </summary>
         const  TRX_TYPE_EBUS_AgentPayment_REQ = "AgentPay";

        /// <summary>
        ///  授权支付签约结果
        /// </summary>
         const  TRX_TYPE_EBUS_AgentSignContract_RESULT = "AgentSignResult";
        /// <summary>
        ///  授权支付批量
        /// </summary>
         const  TRX_TYPE_EBUS_AGENTBATCH_REQ = "AgentBatch";
        /// <summary>
        ///  预授权取消/确认
        /// </summary>
         const  TRX_TYPE_EBUS_PREAUTHCANCELCONFIRM_REQ = "PreAuthCancelConfirm";

        /// <summary>
        ///  商户交易请求类型 - 查询授权支付签约信息
        /// </summary>
         const  TRX_TYPE_QUERYAGENTSIGN = "QueryAgentSign";

        /// <summary>
        ///  授权支付批量结果查询
        /// </summary>
         const  TRX_TYPE_EBUS_AGENTBATCHQUERY_RESULT = "AgentBatchQuery";
         /// <summary>
        ///  商户交易请求类型 - 同步二级商户及账户信息
        /// </summary>
    		 const TRX_TYPE_REG_MERCHANTINFO_REQ = "RegSubMerchantInfo"; 
    		
    		/// <summary>
        ///  商户交易请求类型 - 同步二级商户及账户信息
        /// </summary>
    		 const TRX_TYPE_PAY_REQ_ALIPAY = "AliPayOrderReq";  
    		 
    		/// <summary>
        ///  二级商户内转交易请求
        /// </summary>
    		 const TRX_TYPE_INNER_PAY = "InternalTransfer";  

    		/// <summary>
        ///  二级商户出金交易请求
        /// </summary>
    		 const TRX_TYPE_PAY_FOR_ANOTHER_REQ = "PayForAnother";    
    		 
    		/// <summary>
        ///  二级商户余额查询
        /// </summary>
    		 const TRX_TYPE_SUB_BAL_QRY = "QrySubMerAcctBlc";  
    		 
    		/// <summary>
        ///  垫资户模式对账单下载
        /// </summary>
    		 const TRX_TYPE_SETTLE_SETTLEALIWX_DZH = "SettleAliWxDZH";	
    		 
    		/// <summary>
        ///  内转出金对账单下载
        /// </summary>
    		 const TRX_TYPE_SETTLETRANSFER = "SettleAliWxTransfer";	 
    		 
    		/// <summary>
        ///  内转出金对账单下载
        /// </summary>
    		 const TRX_TYPE_PAY_CANCEL = "MicroPayCancel";	
    		 
    		/// <summary>
        ///  鉴权查询
        /// </summary>
    		 const TRX_TYPE_QUERY_AUTHEN_MERCHANT = "QueryAuthenMerchant";	
    		 
    		/// <summary>
        ///  外转查询
        /// </summary>
    		 const TRX_TYPE_QUERY_TRANSFER_OUT = "QueryTransferOut";	
    		 
    		/// <summary>
        ///  平台对账单下载
        /// </summary>
    		 const TRX_TYPE_SETTLE_PLATFORM = "SettlePlatForm";	
    		
    		/// <summary>
        ///  电子回单下载
        /// </summary>    		 
    		 const TRX_TYPE_GET_RECEIPT = "GetReceipt";


         /// <summary>
         ///  校验随机金额、短信验证码
         /// </summary>
             const TRX_TYPE_VERIFY_SMSANDAMOUNT = "VerifyMessageCodeAndRandomAmount";


          /// <summary>
          ///  担保确认查询
         /// </summary>
             const TRX_TYPE_QRY_PLATFORMCONFIRM = "QueryPlatformConfirm";


         /// <summary>
         ///  二级商户短信验证码重发
         /// </summary>
             const TRX_TYPE_VERIFY_RESENDSMS = "SendMobileMessageForSubMer";

         /// <summary>
         /// 担保确认请求
         /// </summary>
             const TRX_TYPE_PAY_GUANTEEPAY = "GuanteePaySendMQOrder";

         /// <summary>
         ///  充值
         /// </summary>
             const  TRX_TYPE_PAY_DEPOSIT = "Deposit";

        /// <summary>
        ///  平台商户迁移
        /// </summary>
             const  TRX_TYPE_VERIFY_MIGRATMERCHANT = "MigrateSubMerchantInfo";

        /// <summary>
        ///  二级商户状态更新
        /// </summary>
             const   TRX_TYPE_VERIFY_SUBMERSTATUS = "UpdateSubMerchantStatus";

        /// <summary>
        ///  商户交易请求类型 - 同步二级商户及账户信息（新）
        /// </summary>
             const   TRX_TYPE_REG_MERCHANTINFO_REQNEW = "RegSubMerInfo";

        /// <summary>
        ///  二级商户查询
        /// </summary>
             const   TRX_TYPE_QRY_SUBMERCHANTINFO = "QrySubMerchantInfo";

         /// <summary>
         ///  商户交易请求类型 - 被扫支付请求
         /// </summary>
             const   TRX_TYPE_PAY_ABCQRPAY = "ABCQRPayReq";

          /// <summary>
          ///  二级商户证件上传
          /// </summary>
             const   TRX_TYPE_UPLOAD_CERT = "UploadSubMerCert";

         /// <summary>
         ///  二级商户T日可清算金额信息查询
         /// </summary>
             const   TRX_TYPE_SUBMERSETTLEAMOUNT_QUERY = "QuerySubMerTdaySettle";

         /// <summary>
         ///  二级商户T日可清算金额信息查询
         /// </summary>
             const   TRX_TYPE_POSTHIRDFILE_DOWNLOAD = "SettlePOSThirdPartyStatement";

         /// <summary>
         ///  POS订单确认
         /// </summary>
             const   TRX_TYPE_EBUS_POSCONFIRM_REQ = "RegisterPOSOrder";

         /// <summary>
         ///  垫资交易差错文件下载
         /// </summary>
             const   TRX_TYPE_JFERRORFILE_DOWNLOAD = "SettleAdvanceFundTransErrorFile";

         /// <summary>
         ///  第三方订单确认
         /// </summary>
             const   TRX_TYPE_CONFIRM_THIRDORDER = "ThirdConfirmSendMQOrder";

         /// <summary>
         ///  商户交易请求类型 - 批量register
         /// </summary>
             const   TRX_TYPE_OVERDUEREGISTER = "BatchRegister";

         /// <summary>
         ///  商户交易请求类型 - 文件注册register
         /// </summary>
             const   TRX_TYPE_FILEREGISTER = "FileRegister";

         /// <summary>
         ///  商户交易请求类型 - 文件注册register
         /// </summary>
             const   TRX_TYPE_QUERYFILEREGISTER = "QueryFileRegister";
			 
	    /// <summary>
        ///  商户交易请求类型 - 优惠券回退接口
        /// </summary>
             const   TRX_TYPE_SINGLECOUPONFRFUND = "CouponRefund";

        /// <summary>
        ///  商户交易请求类型 - 二级客户信息查询
        /// </summary>
        const   TRX_TYPE_QRY_SUBCUSTOMINFO = "QueryCustomInfo";

        /// <summary>
        ///  商户交易请求类型 - 二级客户交易记录查询
        /// </summary>
        const   TRX_TYPE_QRY_SUBCUSTOMTRANSDETAIL = "QueryCustomTransDetail";

        /// <summary>
        ///  商户交易请求类型 - 二级客户账簿明细查询
        /// </summary>
        const   TRX_TYPE_QRY_SUBCUSTOMACCDETAIL = "QueryCustomAccDetail";
		
        /// <summary>
        ///  商户交易请求类型 - 获取调用凭证
        /// </summary>
        const   TRX_TYPE_GET_WEIXINAUTHINFO = "GetWeiXinAuthInfo";
		
        /// <summary>
        ///  商户交易请求类型 - 代收签约
        /// </summary>
        const   TRX_TYPE_ENTRUSTSIGNREQ = "EntrustSignReq";
		
        /// <summary>
        ///  商户交易请求类型 - 验证码重发（商户端）
        /// </summary>
        const   TRX_TYPE_ENTRUSTSIGN_RESENDSMS = "EntrustSignResendSMS";
		
        /// <summary>
        ///  商户交易请求类型 - 签约确认（商户端）
        /// </summary>
        const   TRX_TYPE_ENTRUSTSIGN_CONFIRM = "EntrustSignConfirm";
		
        /// <summary>
        ///  商户交易请求类型 - 签约查询
        /// </summary>
        const   TRX_TYPE_ENTRUST_QUERYSIGN = "EntrustQuerySign";
		
        /// <summary>
        ///  商户交易请求类型 - 解约
        /// </summary>
        const   TRX_TYPE_ENTRUST_UNSIGN= "EntrustUnSign";
		
        /// <summary>
        ///  商户交易请求类型 - 代收支付单笔扣款
        /// </summary>
        const   TRX_TYPE_ENTRUST_PAY= "EntrustPay";
		
        /// <summary>
        ///  商户交易请求类型 - 代收支付批量扣款
        /// </summary>
        const   TRX_TYPE_ENTRUST_BATCH= "EntrustBatch";
		
        /// <summary>
        ///  商户交易请求类型 - 代收支付批量扣款结果查询
        /// </summary>
        const   TRX_TYPE_ENTRUST_BATCHQUERY= "EntrustBatchQuery";
		
        /// <summary>
        ///  商户交易请求类型 - 大文件对账单下载
        /// </summary>
        const   TRX_TYPE_DOWNLOAD_STATEMENT= "DownloadStatement";
		
        /// <summary>
        ///  商户交易请求类型 - 二级商户交易明细分页查询
        /// </summary>
        const   TRX_TYPE_CHECK_SUBMERACCDETAIL_NEW= "CheckSubMerAccDetailNew";
		
        /// <summary>
        ///  商户交易请求类型 - 保证金账户明细查询
        /// </summary>
        const   TRX_TYPE_CHECK_GUARANTED_FUNDACCDETAIL= "CheckGuarantedFundAccDetail";
		
        /// <summary>
        ///  商户交易请求类型 - 营销补贴资金发放-补贴支付
        /// </summary>
        const   TRX_TYPE_SUBSIDY_TRANSFER= "SubsidyTransfer";
		
        /// <summary>
        ///  商户交易请求类型 - 营销补贴资金发放-补贴退款
        /// </summary>
        const   TRX_TYPE_SUBSIDY_REFUND= "SubsidyRefund";
		
        /// <summary>
        ///  商户交易请求类型 - 营销补贴资金发放-交易查询
        /// </summary>
        const   TRX_TYPE_QUERY_SUBSIDY= "QuerySubsidy";
		
        /// <summary>
        ///  商户交易请求类型 - 二级商户申请单申请状态查询
        /// </summary>
        const   TRX_TYPE_QRYSUBMER_REVIEWSTATUS= "QrySubMerReviewStatus";
		
        /// <summary>
        ///  商户交易请求类型 - 不同主体二级商户迁移
        /// </summary>
        const   TRX_TYPE_MIGRATE_SUBMER_TOOTHERSUBJECT= "MigrateSubMerToOtherSubject";
		
        /// <summary>
        ///  商户交易请求类型 - 第三方订单信息确认(含附言)
        /// </summary>
        const   TRX_TYPE_THIRD_CONFIRM_TRANSLOG= "ThirdConfirmTranslog";
		
        /// <summary>
        ///  商户交易请求类型 - 内转交易查询
        /// </summary>
        const   TRX_TYPE_QUERY_INTERNAL_TRANSFER= "QueryInternalTransfer";
		
        /// <summary>
        ///  商户交易请求类型 - 第三方订单信息确认结果查询
        /// </summary>
        const   TRX_TYPE_QUERY_REALTIME_REGISTER= "QueryRealTimeRegister";
		
        /// <summary>
        ///  商户交易请求类型 - 二级商户交易明细查询
        /// </summary>
        const   TRX_TYPE_QUERY_SUBMER_TRANSDETAIL= "QuerySubMerTransDetail";
		
        /// <summary>
        ///  商户交易请求类型 - 客户账簿支付
        /// </summary>
        const   TRX_TYPE_PLAT_SUBCUSTOM_PAY= "PlatSubCustomPay";
		
        /// <summary>
        ///  商户交易请求类型 - 客户申请状态查询
        /// </summary>
        const   TRX_TYPE_QRY_SUBCUSTOM_REVIEWSTATUS= "QrySubCustomReviewStatus";
		
        /// <summary>
        ///  商户交易请求类型 - 客户信息同步
        /// </summary>
        const   TRX_TYPE_REG_SUBCUSTOMERINFO= "RegSubCustomerInfo";
		
        /// <summary>
        ///  商户交易请求类型 - 校培监管资金划拨申请交易
        /// </summary>
        const   TRX_TYPE_PAYFORANOTHERAPPLY= "PayForAnotherApply";
		
        /// <summary>
        ///  商户交易请求类型 - 校培监管资金划拨结果查询交易
        /// </summary>
        const   TRX_TYPE_FUND_TRANSFERAPPLY_QUERY= "FundTransferApplyQuery";

        /// <summary>
        ///  商户交易请求类型 - 新版二级商户进件
        /// </summary>
        const   TRX_TYPE_SUBMER_REGSUBMERINFONEW= "RegisterSubmerchantInfo";

        /// <summary>
        ///  商户交易请求类型 - 二级商户进件请求撤销
        /// </summary>
        const   TRX_TYPE_SUBMER_CANCELSUBMERAPPLY= "CancelApplyRequest";

        /// <summary>
        ///  商户交易请求类型 - 二级商户补录
        /// </summary>
        const   TRX_TYPE_SUBMER_APPENDSUBMERCHANTINFO= "AppendSubmerchantInfo";

        /// <summary>
        ///  商户交易请求类型 - 扫码支付下单接口
        /// </summary>
         const  TRNX_TYPE_SCAN_PAY_ORDER = "ScanPayOrderReq";

        /// <summary>
        ///  商户交易请求类型 - 二级商户终端信息同步
        /// </summary>
        const   TRX_TYPE_REG_SUBMER_END_INFO= "RegSubMerEndInfo";

        /// <summary>
        ///  二级商户证件上传
        /// </summary>
        const   TRX_TYPE_UPLOAD_CERT_NEW = "UploadSubMerCertNew";

        /// <summary>
        ///  商户交易请求类型 - 扫码支付下单接口（线上）
        /// </summary>
         const  TRNX_TYPE_OLSCAN_PAY_ORDER = "OLScanPayOrderReq";
}
