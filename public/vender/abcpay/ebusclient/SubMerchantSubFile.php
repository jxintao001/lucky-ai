<?php
class_exists('TrxRequest') or require (dirname(__FILE__) . '/core/TrxRequest.php');
class_exists('Json') or require (dirname(__FILE__) . '/core/Json.php');
class_exists('IChannelType') or require (dirname(__FILE__) . '/core/IChannelType.php');
class_exists('IPaymentType') or require (dirname(__FILE__) . '/core/IPaymentType.php');
class_exists('INotifyType') or require (dirname(__FILE__) . '/core/INotifyType.php');
class_exists('DataVerifier') or require (dirname(__FILE__) . '/core/DataVerifier.php');
class_exists('ILength') or require (dirname(__FILE__) . '/core/ILength.php');
class_exists('IPayTypeID') or require (dirname(__FILE__) . '/core/IPayTypeID.php');
class_exists('ICommodityType') or require (dirname(__FILE__) . '/core/ICommodityType.php');
class_exists('IIsBreakAccountType') or require (dirname(__FILE__) . '/core/IIsBreakAccountType.php');

class SubMerchantSubFile extends TrxRequest{

    public $request = array (
        "TrxType" => IFunctionID :: TRX_TYPE_UPLOAD_CERT,
        "SubMerNo" => "",
        "SubMerCertFile" => "",
        "File" => "111"
    );

    function __construct() {
    }

    //把压缩文件转为base64
    public function ZipFileToBase64String($filename){
        $base64 = null;
      //  $fileinfo = filesize($filename);
      //  $base64 = 'data'.fileinfo['mime'].';base64'.chunk_split(base64_encode($fileinfo));

        $fileinfo = fopen($filename,"r");
        if($fileinfo){
            $filesize = filesize($filename);
            $content = fread($fileinfo,$filesize);

          /*  while(!feof($fileinfo)){
                $data[] = fread($fileinfo,$filesize);
            }
          */
            $base64 = chunk_split(base64_encode($content));

        }
        fclose($fileinfo);
        return $base64;

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

        if (empty($this->request["SubMerNo"]))
            throw new TrxException(TrxException :: TRX_EXC_CODE_1100, TrxException :: TRX_EXC_MSG_1100, "未设定二级商户名称！");
        if (empty($this->request["Flag"]))
            throw new TrxException(TrxException :: TRX_EXC_CODE_1100, TrxException :: TRX_EXC_MSG_1100, "未设定标志位！");


    }


}