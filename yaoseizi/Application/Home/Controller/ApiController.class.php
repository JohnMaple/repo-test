<?php
/*
 *      _______ _     _       _     _____ __  __ ______
 *     |__   __| |   (_)     | |   / ____|  \/  |  ____|
 *        | |  | |__  _ _ __ | | _| |    | \  / | |__
 *        | |  | '_ \| | '_ \| |/ / |    | |\/| |  __|
 *        | |  | | | | | | | |   <| |____| |  | | |
 *        |_|  |_| |_|_|_| |_|_|\_\\_____|_|  |_|_|
 */
/*
 *     _________  ___  ___  ___  ________   ___  __    ________  _____ ______   ________
 *    |\___   ___\\  \|\  \|\  \|\   ___  \|\  \|\  \ |\   ____\|\   _ \  _   \|\  _____\
 *    \|___ \  \_\ \  \\\  \ \  \ \  \\ \  \ \  \/  /|\ \  \___|\ \  \\\__\ \  \ \  \__/
 *         \ \  \ \ \   __  \ \  \ \  \\ \  \ \   ___  \ \  \    \ \  \\|__| \  \ \   __\
 *          \ \  \ \ \  \ \  \ \  \ \  \\ \  \ \  \\ \  \ \  \____\ \  \    \ \  \ \  \_|
 *           \ \__\ \ \__\ \__\ \__\ \__\\ \__\ \__\\ \__\ \_______\ \__\    \ \__\ \__\
 *            \|__|  \|__|\|__|\|__|\|__| \|__|\|__| \|__|\|_______|\|__|     \|__|\|__|
 */
// +----------------------------------------------------------------------
// | ThinkCMF [ WE CAN DO IT MORE SIMPLE ]
// +----------------------------------------------------------------------
// | Copyright (c) 2013-2014 http://www.thinkcmf.com All rights reserved.
// +----------------------------------------------------------------------
// | Author: Dean <zxxjjforever@163.com>
// +----------------------------------------------------------------------
namespace Home\Controller;
use Think\Controller;
require_once VENDOR_PATH.'wechat/wechat.class.php';
require_once VENDOR_PATH.'wechat/com/TPWechat.class.php';
/**
 * 首页
 */
class ApiController extends Controller {

    protected $weObj;

    public function index() {
        $options = C('weixin');
        $this->weObj = new \TPWechat($options);
        $this->weObj->valid();
        $type = $this->weObj->getRev()->getRevType();
        switch ($type) {
            case \TPWechat::MSGTYPE_TEXT:
                $content = $this->weObj->getRev()->getRevContent();
                if($content == 'wifi'){
                    $this->weObj->text("wifi")->reply();
                }else{
                    $this->weObj->text("感谢您的留言")->reply();
                }
                exit;
                break;
            case \TPWechat::MSGTYPE_EVENT:
                $revEvent = $this->weObj->getRev()->getRevEvent();
                if($revEvent['event'] == \TPWechat::EVENT_SUBSCRIBE){
                    $this->weObj->text("客官，您终于来了~")->reply();
                    $openid = $this->weObj->getRev()->getRevFrom();
                    //$user_id = D("Common/Weimember")->sync_member($openid);
                    //cookie('weixin_user',$user_id,864000);
                    cookie('weixin_openid',$openid,864000);
                }elseif($revEvent['event'] == \TPWechat::EVENT_MENU_CLICK){
                    if($revEvent['key'] == 'wifi'){
                        $wifi = M("wifi")->select();
                        $wifi_list = '';
                        if($wifi){
                            foreach($wifi as $item){
                                $wifi_list[] .="wifi名称：".$item['name']."\r\n"."wifi密码：".$item['pass']."\r\n";
                            }
                        }
                        $this->weObj->text(implode("===============\r\n",$wifi_list))->reply();
                    }elseif($revEvent['key'] == 'concatus'){
                        $this->weObj->text("客服电话：\r\n0591-81234567")->reply();
                    }
                }
                exit;
                break;
            case \TPWechat::MSGTYPE_IMAGE:
                break;
            default:
                $this->weObj->text("help info")->reply();
        }
        /**   //设置菜单
         *   $newmenu =  array(
         *        "button"=>
         *            array(
         *                array('type'=>'click','name'=>'最新消息','key'=>'MENU_KEY_NEWS'),
         *                array('type'=>'view','name'=>'我要搜索','url'=>'http://www.baidu.com'),
         *                )
         *        );
         *   $result = $weObj->createMenu($newmenu);
         *
         * }
         *
         *
         * 检查用户登录
         */
    }
}


