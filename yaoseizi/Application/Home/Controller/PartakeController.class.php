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

/**
 * 分享赚钱控制器
 * 主要获取首页聚合数据
 */
class PartakeController extends HomeController {

	//分享赚钱图片
    public function index(){
        $member = M('ucenter_member')->where('id='.$this->uid)->find();
        $this->assign('member',$member);
        $this->display();
    }
}