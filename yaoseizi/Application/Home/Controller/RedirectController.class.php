<?php
// +----------------------------------------------------------------------
// | OneThink [ WE CAN DO IT JUST THINK IT ]
// +----------------------------------------------------------------------
// | Copyright (c) 2013 http://www.onethink.cn All rights reserved.
// +----------------------------------------------------------------------
// | Author: 麦当苗儿 <zuojiazi@vip.qq.com> <http://www.zjzit.cn>
// +----------------------------------------------------------------------

namespace Home\Controller;
use OT\DataDictionary;
use Think\Controller;
use User\Api\UserApi;
require_once VENDOR_PATH.'wechat/wechat.class.php';
require_once VENDOR_PATH.'wechat/com/TPWechat.class.php';
require_once VENDOR_PATH.'wechat/com/Wxauth.class.php';
/**
 * 前台首页控制器
 * 主要获取首页聚合数据
 */
class RedirectController extends Controller {
    public $wx;
	//系统首页
    public function index(){

        //TODO:判断手机+微信打开

        $check_time = M('url_check')->getField('reg_time');
        $redirect_url = M('url')->where('status=2')->getField('url');
        if($check_time + 300 < time()){
            M('url_check')->where("id=1")->save(array('reg_time'=>time()));
            if(!empty($redirect_url)){
                require_once VENDOR_PATH . 'DomainDetech.php';
                $app_id     = 'o3KnIv6RrRDb-fKarj1tK5fVI4mU'; //填写你的令牌
                $app_secret = 'baibaoxiang123'; //填写你设置的密码
                $domain_obj = new \DomainDetech($app_id, $app_secret);
                $rs         = $domain_obj->run($redirect_url);
                if($rs['status'] == -1){
                    M('url')->where(array('url'=>$redirect_url))->save(array('status'=>0));
                    $redirect_url = M('url')->where('status=2')->getField('url');
                }
            }
        }

   /*     require_once VENDOR_PATH . 'WxpayAPI/lib/WxPay.Api.php';
        require_once VENDOR_PATH . 'WxpayAPI/lib/WxPay.Config.php';
        require_once VENDOR_PATH . 'WxpayAPI/example/WxPay.JsApiPay.php';
        $tools = new \JsApiPay();
        $openid = $tools->GetOpenid();*/
        $options = array(
            'token' => 'txapi', //填写你设定的key
            'encodingaeskey' => 'vRFsFCNEWkMY4biy8qTDCzwUAgdZoDMIwegfjP7r4Rn', //填写加密用的EncodingAESKey
            'appid' => 'wxd4ad93659f0d1c90', //填写高级调用功能的app id
            'appsecret' => '45f7fb100447cefed6b113ebbf7e6267' //填写高级调用功能的密钥
        );
    //session('[destroy]');exit;
        $this->wx = new \Wxauth($options);
        $openid =  $this->wx->open_id;
		
        /* 调用注册接口注册用户 */
        $User = new UserApi;
        $info = $User->infoByOpenid($openid);
        if(is_numeric($info ) && !$info ){
            $uid  = $User->registerByOpenid($openid);
            //todo:分销
            $parent_uid = (int)I('get.uid');
            if($parent_uid && $uid){
                M('ucenter_member')->where('id='.$uid)->save(array('puid'=>$parent_uid));
            }
        }else{
            $uid = $info['id'];
        }
        $info =  M('ucenter_member')->where('id='.$uid)->find();
        if(false){
            require_once VENDOR_PATH . 'WxpayAPI/lib/WxPay.Api.php';
            require_once VENDOR_PATH . 'WxpayAPI/lib/WxPay.Config.php';
            require_once VENDOR_PATH . 'WxpayAPI/example/WxPay.JsApiPay.php';
            $tools = new \JsApiPay();
            $openid = $tools->GetOpenid();
            if($openid){
                M('ucenter_member')->where('id='.$uid)->save(array('wx_openid2'=>$openid));
            }
        }
        if(empty($info['qr_code'])){
            //生成二维码
            $path = 'Uploads/Download/'.time().'.png';
            qrcode('http://www.fulaozhongyi.com/index.php?s=/Home/Redirect/index/uid/'.$uid,$path);
            //图片合成
            $bg_image = 'Public/Home/images/M/code.png';
            $path_to = 'Uploads/Download/'.time().'_share.png';

            image_copy_image($bg_image,$path,230,396,277,274,$path_to);

            M('ucenter_member')->where('id='.$uid)->save(array('qr_code'=>$path_to));
        }

        $sign = md5($redirect_url.$openid.$uid);
        M('ucenter_member')->where('id='.$uid)->save(array('sign'=>$sign));
       redirect($redirect_url.'/sign/'.$sign);
        // echo $openid;
	//dump(session('uid'));die;

    }
}