<?php

require_once VENDOR_PATH ."WxpayAPI/lib/WxPay.Api.php";
require_once VENDOR_PATH ."WxpayAPI/lib/WxPay.Config.php";
require_once VENDOR_PATH ."WxpayAPI/lib/WxPay.Data.php";
/**
 * 
 * App支付实现类
 */


class AppPay{
    
    
        /**
	 * 
	 * 获取app支付的参数
	 * @param array $UnifiedOrderResult 统一支付接口返回的数据
	 * @throws WxPayException
	 * 
	 */
	public function GetAppParameters($UnifiedOrderResult)
	{
		$app = new WxPayAppPay();
                
                $data['appid'] = $UnifiedOrderResult["appid"];
                $data['partnerid'] = $UnifiedOrderResult["mch_id"];
                $data['prepayid'] = $UnifiedOrderResult["prepay_id"];
                $data['package'] = 'Sign=WXPay';
                $data['noncestr'] = \WxPayApi::getNonceStr();
                $timeStamp = time();
                $data['timestamp'] = $timeStamp;
                ksort($data);
                foreach ($data as $k => $v)
		{
			if($k != "sign" && $v != "" && !is_array($v)){
				$buff .= $k . "=" . $v . "&";
			}
		}
		
		$buff = trim($buff, "&");
                $str = $buff;
                $buff = $buff.'&key='.WxPayConfig::KEY;
                $sign = strtoupper(MD5($buff));
                $str .= '&sign='.$sign;
                $data['packageValue'] = 'Sign=WXPay';
                unset($data['package']);
                $data['sign'] = $sign;
                $parameters = json_encode($data);
		return $parameters;
	}
    
    
}