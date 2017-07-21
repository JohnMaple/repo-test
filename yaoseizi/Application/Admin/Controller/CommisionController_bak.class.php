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
        o($map,1);
        //查询总记录数 分页
        $tote = M()
            ->table('sezi_commision as c')
            ->join('left join sezi_user as u on c.uid=u.id')
            ->where($map)
            ->field('c.id,u.open_id,c.one_uid,c.one_brokerage,c.two_uid,c.two_brokerage,c.three_uid,c.three_brokerage,c.tote_brokerage,c.current_date')
            ->count();
        $page = new \Think\Page($tote,20);
        $show = $page->show();
        /*/*******************************求1级徒弟的总个数*****************************/
        $result=M('user') ->select();


        $list[] =array();
        foreach ($result as $key=>$value){
            $res = M('user')->where('pid='.$value['open_id'])->select();
            $tote = count($res);
            $list[] = $tote;
            //echo $tote."<br>";
        }
        //o($list,1);

        /*/************************求1级徒弟给我带来的佣金 = 我所有1级徒弟的总数 的投注金额加在一起*佣金率*************/
        //先找出都哪些是我的一级徒弟
        $result=M('user')->select();
        $ress[] =array();
        foreach ($result as $key=>$value){
            $res = M('user')->where('pid=1882517')->select(); //我的1级徒弟，是id=2和11的
            $ress = $res;
        }
        //取出用户表$res中的id 作为投注表uid 的查询条件 = 我的每个1级徒弟的消费金额
        $id = array_column($ress,'id');
        o($id,1);
        $where['uid'] = $id;        //这里结果只有2和11为什么不能当查询条件？？？
        $model = M('cathectic')->where('uid=2')->select();  //不能使用$id必须写死，是为什么？？
        o($model,1);

        $money_tote = array_column($model,'money');     //取出uid = 2的，每笔消费金额，循环相加*佣金率
        o($money_tote,1);
        $vs = 0;    //消费总额3100
        foreach($money_tote as $k => $v){
            $vs = $vs + $v;
        }
        //消费总额 * 佣金率10%
        $one_money = $vs * 0.1;     //我的所有1级徒弟 给我带来了310佣金。这个佣金还要在加2次，然后给总佣金
        o($one_money,1);




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