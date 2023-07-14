<?php
/**
 * Description of SelectUrl
 *
 * @author Administrator
 */
class SelectUrl {
    /**
     * 
     * @param type $IsTest  正式（true）/测试(false)
     * @param int $InterfaceType   1(PC),2(WAP)
     * @param type $RequestType   1(交易接口),2(订单查询),3(绑定ID查询)
     * @return string
     */
    public static function Url($IsTest,$type=1){
        switch ($type){
        case 1:
            $UrlString="https://tgw.baofoo.com/platform/gateway/front";
            if($IsTest){
                $UrlString = "https://public.baofoo.com/platform/gateway/front";
            }
            break;
        case 2:
            $UrlString="https://tgw.baofoo.com/platform/gateway/back";
            if($IsTest){
                $UrlString = "https://public.baofoo.com/platform/gateway/back";
            } 
        }
        RETURN $UrlString;
    }
    
    public static function ApiUrl(){
       $UrlString ="https://public.baofoo.com/platform/gateway/back";
       return $UrlString;
    }
}
