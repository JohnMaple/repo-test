<?php
// +----------------------------------------------------------------------
// | OneThink [ WE CAN DO IT JUST THINK IT ]
// +----------------------------------------------------------------------
// | Copyright (c) 2013 http://www.onethink.cn All rights reserved.
// +----------------------------------------------------------------------
// | Author: 麦当苗儿 <zuojiazi@vip.qq.com> <http://www.zjzit.cn>
// +----------------------------------------------------------------------

namespace Home\Controller;
use Think\Controller;
require_once VENDOR_PATH.'wechat/wechat.class.php';
/**
 * 前台公共控制器
 * 为防止多分组Controller名称冲突，公共Controller名称统一使用分组名称
 */
class HomeController extends Controller {

    public $openid = '';
    public $uid = 0;
	/* 空操作，用于输出404页面 */
//	public function _empty(){
//		$this->redirect('Index/index');
//	}


    protected function _initialize(){
//session_unset();session_destroy();die;
        /* 读取站点配置 */
        $config = api('Config/lists');
        C($config); //添加配置;

        if(!C('WEB_SITE_CLOSE')){
            $this->error('站点已经关闭，请稍后访问~');
        }
        //TODO:判断手机+微信打开
        $options = array(
            'token' => 'txapi', //填写你设定的key
            'encodingaeskey' => 'vRFsFCNEWkMY4biy8qTDCzwUAgdZoDMIwegfjP7r4Rn', //填写加密用的EncodingAESKey
            'appid' => 'wxd4ad93659f0d1c90', //填写高级调用功能的app id
            'appsecret' => '45f7fb100447cefed6b113ebbf7e6267' //填写高级调用功能的密钥
        );
        $this->uid = (int)session('uid');
        $this->openid = session('openid');
        $wxchat = new \wechat($options);
        dump($wxchat);
        $info =  M('ucenter_member')->where('id=2')->find();
        //$access_token = $wxchat->checkAuth();
        //var_dump($access_token);exit;
        //$res=$wxchat->getOauthUserinfo($access_token,$info['openid']);
        //dump($res);die;
	//dump($this->openid);die;
      /*  $this->uid = 1;
        $this->openid = 'dsfdsf';*/

        $sign = I('get.sign');
        if(!$this->uid){
//            if(empty($sign)){
//                die('<h3 style="text-align:center">请用分享二维码打开！</h3>');
//            }
            $user_info = M('ucenter_member')->where(array('sign'=>$sign))->find();
            dump($this->uid);
            if(!$user_info){
                dump(1);
                die();
            }

            $this->uid = $user_info['id'];
            $this->openid = $user_info['wx_openid'];
            session('uid',$this->uid);
            session('openid',$this->openid);
			redirect(U('Home/Index/index'));
        }elseif($this->uid){
			if(!empty($sign)){
				redirect(U('Home/Index/index'));
			}
		}
    }

	/* 用户登录检测 */
	protected function login(){
		/* 用户登录检测 */
		is_login() || $this->error('您还没有登录，请先登录！', U('User/login'));
	}

}
