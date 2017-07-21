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

//投注记录控制器
class AnalyseController extends AdminController {

    //报表
    public function index(){
        //算今日使用的时间戳
        $j = strtotime(date('Y-m-d',time()));   //今日0点时间戳
        $m = $j + 3600*24;                             //明日0点时间戳
        //算本月使用的时间戳
        $month = mktime(0,0,0,date('m'),1,date('Y'));    //本月1日0点时间戳
        $before_one = time(); //当前时间戳
        //报表日期
        $report_forms = date('Y-m-d',time());

        /* 今日总用户数：用户表的登录时间截取
           投注总额： 投注表，累加今日投注金额
           中奖总额： 提现表，累加今日中奖金额
           今日毛利润：  投注金额-中奖金额 */

        //今日放款人数： status = 4(放款成功) && load_time(放款时间)>=今天0点的时间 && 放款时间<明天0点的时间 && uid去除重复的，总共有多少个
        $today_loan_person  = count(M()->query("SELECT uid,status,load_time FROM qzz_money WHERE status=4 AND load_time>='$j'  AND load_time<'$m' GROUP BY uid"));

        //本月放款人数： status = 4(放款成功) && load_time(放款时间)>=本月1日0点的时间 && 放款时间<当前时间 && uid去除重复的，总共有多少个
        $month_loan_person  = count(M()->query("SELECT uid,status,load_time FROM qzz_money WHERE status=4 AND load_time>='$month'  AND load_time<'$before_one' GROUP BY uid"));
        //本月毛利润：   利息(interest)+续借费用(renew_cost)+综合费用(fee) && application_time(申请时间)>=本月1日0点的时间 && 申请时间<当前时间
        //取利息总和
        $where['application_time'] = array(array('egt',$month), array('lt',$before_one));
        $lixi = M('money')->where($where)->sum('interest');
        //续借费用总和
        $xujie = M('money')->where($where)->sum('renew_cost');
        //综合费用总和
        $zonghe = M('money')->where($where)->sum('fee');
        //毛利润
        $profit = $lixi + $xujie + $zonghe;

        //累计放款笔数： 所有的 status = 4(放款成功)
        $add_up_fun = count(M('money')->where('status=4')->select());
        //累计未放款笔数：所有的 status = 3(审核失败)
        $add_up_nofun = count(M('money')->where('status=3')->select());



        $this->assign('rq',$report_forms);    //报表日期
        $this->assign('tlp',$today_loan_person);    //今日放款人数
        $this->assign('trp',$today_renew_person);   //今日续借人数
        $this->assign('typ',$today_yet_person);     //今日还款人数
        $this->assign('tnp',$today_new_person);     //今日新增人数
        $this->assign('tnop',$today_new_one_person);//今日新增1星人数
        $this->assign('mlp',$month_loan_person);    //本月放款人数
        $this->assign('mlr',$profit);               //本月毛利润
        $this->assign('auf',$add_up_fun);           //累计放款笔数
        $this->assign('auof',$add_up_nofun);        //累计未放款笔数

        $this->display();


    }
}