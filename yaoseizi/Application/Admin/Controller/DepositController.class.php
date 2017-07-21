<?php
// +----------------------------------------------------------------------
// | OneThink [ WE CAN DO IT JUST THINK IT ]
// +----------------------------------------------------------------------
// | Copyright (c) 2013 http://www.onethink.cn All rights reserved.
// +----------------------------------------------------------------------
// | Author: huajie <banhuajie@163.com>
// +----------------------------------------------------------------------
namespace Admin\Controller;
use Admin\Model\AuthGroupModel;
use Think\Page;

//提现记录控制器
class DepositController extends AdminController {

    //显示提现记录
    public function index(){

        $map = array();
        $status = I('get.status',-1,'intval');
        $agency_id = intval(session('user_auth.agency_id'));
        if($status >= 0){
            $map['d.is_deposit'] = $status;
        }
        $all_ids = array();
        if($agency_id){
            $all_ids = all_childs($agency_id);
        }
        if(!is_administrator()){
            $map[] = array('uid'=>array('in',$all_ids));
        }
        $tote = M('deposit')->alias('d')->where($map)->count();

        $page = new \Think\Page($tote,20);
        $show = $page->show();

        $list = M('deposit')
            ->alias('d')
            ->join('LEFT JOIN __UCENTER_MEMBER__ u ON d.uid=u.id')
            ->field('d.*,u.username')
            ->where($map)
            ->order('d.id DESC')
            ->select();
        foreach ($list as &$item){
            $item['uuid']  = str_pad($item['uid'],7,'0',STR_PAD_LEFT);
        }
        $this->assign('count',$tote);
        $this->assign('page',$show);
        $this->assign('list',$list);
        $this->display();
    }

    private function cathecticWhere(){
        $title = I('get.title');        //接收标题
        $t_sta = I('get.time_start');   //接收开始时间
        $t_end = I('get.time_end');     //接收结束时间


        //如果接收到标题就赋值给u.open_id字段 进行模糊查询
        if($title){
            $where['u.open_id'] = array('like',array("%$title%"));
        }

        //根据创建时间查询
        if ($t_sta || $t_end) {
            //如果没有输入开始时间（只有一个结束时间的话）默认查询当前日前一个月的
            $start_time = ['egt', strtotime($t_sta)];
            empty($t_sta) && $start_time = ['egt', time() - 3600 * 24 * 30];
            //如果没有输入结束时间，就以当前时间为结束时间
            $end_time = ['elt', strtotime($t_end)];
            empty($t_end) && $end_time = ['elt', time()];
            //输入开始时间-结束时间
            $where['c.current_date'] = array($start_time, $end_time);
        }

        return $where;
    }

    //客服审核 是按钮,返回操作成功
    public function yAuditing(){
       $map['id'] = I('post.id');
        //客服审核通过
        $z = M('deposit')->where($map)->filter('strip_tags')->setField('is_deposit','1');

        //返回操作成功
        if($z == 0){
            $msg = array('error' => 1, 'msg' => '请勿重复操作');
            $this->ajaxReturn($msg);
        }else if($z == 1){
            $msg = array('error' => 2, 'msg' => '操作成功');
            $this->ajaxReturn($msg);
        }else{
            $msg = array('error' => 3, 'msg' => '操作失败');
            $this->ajaxReturn($msg);
        }
    }

    //客服审核 否按钮
    public function nAuditing(){
        $map['id'] = I('post.id');
        //客服审核通过
        $z = M('deposit')->where($map)->filter('strip_tags')->setField('is_deposit','2');

        //返回操作成功
        if($z == 0){
            $msg = array('error' => 1, 'msg' => '请勿重复操作');
            $this->ajaxReturn($msg);
        }else if($z == 1){
            $msg = array('error' => 2, 'msg' => '操作成功');
            $this->ajaxReturn($msg);
        }else{
            $msg = array('error' => 3, 'msg' => '操作失败');
            $this->ajaxReturn($msg);
        }
    }
}