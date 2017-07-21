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

//用户记录控制器
class MemberController extends AdminController {

    //显示投注记录
    public function index(){
        $map = array();
        $id = I('get.title',0,'intval');
        $agency_id = intval(session('user_auth.agency_id'));
        $all_ids = array();
        if($agency_id){
            $all_ids = all_childs($agency_id);
        }
        if(!is_administrator()){
            $map[] = array('id'=>array('in',$all_ids));
        }
        if($id){
            $map[] = array('id'=>$id);
        }
        /*//查询总记录数 分页
        $tote = M('ucenter_member')->where($map)->count();

        $page = new \Think\Page($tote,200);
        $show = $page->show();*/


        $Data=M('ucenter_member');// 实例化grab_order对象
        // 查询满足要求的总记录数
        $msg = $Data->where($map)->select();
        $count=count($msg);
        //dump($count) ;exit;
        $Page = new \Think\Page($count,100);// 实例化分页类 传入总记录数和每页显示的记录数8条
        $show = $Page->show();// 分页显示输出
        // 进行分页数据查询
        $list=$Data->where($map)->order('reg_time desc')->limit($Page->firstRow.','.$Page->listRows)->select(); 

        // dump($list);die;
        // $list = $Data->where($map)->order('reg_time desc')->limit($Page->firstRow.','.$Page->listRows)->select();
        foreach ($list as &$item){
            $item['username']  = str_pad($item['id'],7,'0',STR_PAD_LEFT);
            $item['puid']  = str_pad($item['puid'],7,'0',STR_PAD_LEFT);
            //总充值
            $item['recharge'] = M('recharge')->where('uid='.$item['id'].' AND is_success=1')->sum('money');
            $item['deposit'] = M('deposit')->where('uid='.$item['id'])->sum('money');
        }
        $this->assign('count',$count);
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
}