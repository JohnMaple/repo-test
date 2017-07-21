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

//开奖记录控制器
class AwardController extends AdminController {

    //显示开奖记录
    public function index(){
        $map = $this->cathecticWhere();

        //查询总记录数 分页
        $tote = M('award')->where($map)->count();

        $page = new \Think\Page($tote,20);
        $show = $page->show();

        $list = M('award')
            ->where($map)
            ->order('id desc')
            ->limit($page->firstRow.','.$page->listRows)
            ->select();

        $this->assign('count',$tote);
        $this->assign('page',$show);
        $this->assign('list',$list);
        $this->display();
    }

    private function cathecticWhere(){

        $t_sta = I('get.time_start');   //接收开始时间
        $t_end = I('get.time_end');     //接收结束时间

       /* $title = I('get.title');        //接收标题
        //如果接收到标题就赋值给u.open_id字段 进行模糊查询
        if($title){
            $where['u.open_id'] = array('like',array("%$title%"));
        }*/

        //根据创建时间查询
        if ($t_sta || $t_end) {
            //如果没有输入开始时间（只有一个结束时间的话）默认查询当前日前一个月的
            $start_time = ['egt', strtotime($t_sta)];
            empty($t_sta) && $start_time = ['egt', time() - 3600 * 24 * 30];
            //如果没有输入结束时间，就以当前时间为结束时间
            $end_time = ['elt', strtotime($t_end)];
            empty($t_end) && $end_time = ['elt', time()];
            //输入开始时间-结束时间
            $where['current_date'] = array($start_time, $end_time);
        }

        return $where;
    }
}