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

//佣金记录控制器
class commisionController extends AdminController {

    //显示投注记录
    public function index(){

        $map = $this->cathecticWhere();
        $agency_id = intval(session('user_auth.agency_id'));
        if($agency_id){
        }
        //查询总记录数 分页
        $tote = M()
            ->table('sezi_commision as c')
            ->join('left join sezi_user as u on c.uid=u.id')
            ->where($map)
            ->field('c.id,u.open_id,c.one_uid,c.one_brokerage,c.two_uid,c.two_brokerage,c.three_uid,c.three_brokerage,c.tote_brokerage,c.current_date')
            ->count();

        $page = new \Think\Page($tote,20);
        $show = $page->show();



        $list = M()
            ->table('sezi_commision as c')
            ->join('left join sezi_user as u on c.uid=u.id')
            ->where($map)
            ->field('c.id,u.open_id,c.one_uid,c.one_brokerage,c.two_uid,c.two_brokerage,c.three_uid,c.three_brokerage,c.tote_brokerage,c.current_date')
            ->select();
        //o($list,1);
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
}