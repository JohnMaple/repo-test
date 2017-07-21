<?php
// +----------------------------------------------------------------------
// | OneThink [ WE CAN DO IT JUST THINK IT ]
// +----------------------------------------------------------------------
// | Copyright (c) 2013 http://www.onethink.cn All rights reserved.
// +----------------------------------------------------------------------
// | Author: 麦当苗儿 <zuojiazi@vip.qq.com> <http://www.zjzit.cn>
// +----------------------------------------------------------------------

namespace Admin\Controller;
use User\Api\UserApi as UserApi;

/**
 * 后台首页控制器
 * @author 麦当苗儿 <zuojiazi@vip.qq.com>
 */
class IndexController extends AdminController {

    /**
     * 后台首页
     * @author 麦当苗儿 <zuojiazi@vip.qq.com>
     */
    public function index(){
        $map = array();
        $agency_id = intval(session('user_auth.agency_id'));

        $all_ids = array();
        if($agency_id){
            $all_ids = all_childs($agency_id);
        }
        $admin_path = 'Uploads/share_imgs/s'.$agency_id.'/';
        if(!is_dir($admin_path)){
            mkdir($admin_path);
            for($i=1;$i<=8;$i++){
                //生成二维码
                $path = $admin_path.time().rand(1000,9999).'.png';
                qrcode('http://www.fulaozhongyi.com/index.php?s=/Home/Redirect/index/uid/'.$agency_id,$path);
                //图片合成
                $bg_image = 'Uploads/share_imgs/M/'.$i.'.png';
                $path_to = $admin_path.$i.'_share.png';

                image_copy_image($bg_image,$path,230,396,277,274,$path_to);
                @unlink($path);
            }

        }
        /*if($agency_id){
            $all_agency_ids = all_agency_childs($agency_id);
        }
        if(!is_administrator()){
            $map[] = array('uid'=>array('in',$all_ids));
            $ba_where[] = array('id'=>array('in',$all_ids));
        }
        $memeber_result = M('member')->field('fanli')->where(array('agency_id'=>$agency_id))->find();
        //統計所有记录
        //充值总金額
        $re_where = array_merge($map,array('c.is_success'=>1));
        $all_remoney =  M('recharge')->alias('c')->where($re_where)->sum('money');
        //提现总金额
       // $de_where = array_merge($map,array('c.is_deposit'=>1));
        $all_demoney = M('deposit')->alias('c')->where($map)->sum('money');
        //总余额
        $all_bamoney = M('ucenter_member')->alias('c')->where($ba_where)->sum('money');


        //統計当天的记录
        //当天日期
        $cur_date = strtotime(date('Y-m-d'));
        //充值
        $re_where['c.create_time'] = array('egt',$cur_date);
        $re_where = array_merge($re_where,array('c.is_success'=>1));
        $cur_remoney =  M('recharge')->alias('c')->where($re_where)->sum('money');
        //提现
        $map['c.create_time'] = array('egt',$cur_date);
        $cur_demoney =  M('deposit')->alias('c')->where($map)->sum('money');

        //昨天的余额
        $yestoday = date("Y-m-d",strtotime("-1 day"));
        $income_result = M('income')->field('cur_remoney,cur_demoney,all_bamoney,commission') ->where(array('uid'=>$agency_id,'date'=>$yestoday))->find();
        $income_result_agency = M('income')->where(array('uid'=>array('in',$all_agency_ids),'date'=>$yestoday))->field('commission,uid')->select();
        foreach ($income_result_agency as &$item){
            $item['uid'] = str_pad($item['uid'],7,'0',STR_PAD_LEFT);
        }
        $two_yestoday = date("Y-m-d",strtotime("-2 day"));
        $two_income_result = M('income')->field('all_bamoney') ->where(array('uid'=>$agency_id,'date'=>$two_yestoday))->find();

        $this->assign('all_remoney',$all_remoney);
        $this->assign('all_demoney',$all_demoney);
        $this->assign('all_bamoney',$all_bamoney);
        $this->assign('cur_remoney',$cur_remoney);
        $this->assign('cur_demoney',$cur_demoney);
        $this->assign('income_result',$income_result);
        $this->assign('income_result_agency',$income_result_agency);
        $this->assign('two_income_result',$two_income_result);
        $this->assign('memeber_result',$memeber_result);*/
        $memeber_result = M('member')->field('fanli')->where(array('agency_id'=>$agency_id))->find();
        $fanli = $memeber_result['fanli'];

        //总充值
        if(!is_administrator()){
            $re_where['c.uid'] = array('in',$all_ids);
        }
        $re_where['c.is_success'] = 1;
        $cur_remoney =  M('recharge')->alias('c')->where($re_where)->sum('money');
        $cur_remoney = empty($cur_remoney) ? 0 : $cur_remoney;
        //总提现
        if(!is_administrator()) {
            $de_where['c.uid'] = array('in', $all_ids);
        }
       // $de_where['c.is_deposit'] = 1;
        $cur_demoney =  M('deposit')->alias('c')->where($de_where)->sum('money');
        $cur_demoney = empty($cur_demoney) ? 0 : $cur_demoney;
        //账户总余额
        if(!is_administrator()) {
            $ba_where['c.id'] = array('in', $all_ids);
        }
        $all_bamoney = M('ucenter_member')->alias('c')->where($ba_where)->sum('money');
        $all_bamoney = empty($all_bamoney) ? 0 : $all_bamoney;

        //客损
        $c_money =  ($cur_remoney - $all_bamoney - $cur_demoney)*$fanli/100;
        //历史客损记录
        $lists  =  M('income2') ->where(array('uid'=>$agency_id))->limit(6)->select();

        $today = strtotime(date('Y-m-d',time()));
        if(!is_administrator()) {
            $ba_where1['c.uid'] = array('in', $all_ids);
        }
        $ba_where1['c.create_time'] = array('egt',$today);
        $ba_where1['c.is_success'] = 1;
        $today_recharge = M('recharge')->alias('c')->where($ba_where1)->sum('money');
        unset( $ba_where1['c.is_success']);
      //  $ba_where1['c.is_deposit'] = 1;
        $today_deposit = M('deposit')->alias('c')->where($ba_where1)->sum('money');
        if(UID){
            $this->assign('cur_remoney',$cur_remoney);
            $this->assign('cur_demoney',$cur_demoney);
            $this->assign('all_bamoney',$all_bamoney);
            $this->assign('c_money',$c_money);
            $this->assign('lists',$lists);
            $this->assign('today_recharge',$today_recharge);
            $this->assign('today_deposit',$today_deposit);
            $this->assign('memeber_result',$memeber_result);
            $this->assign('admin_path',$admin_path);
            $this->meta_title = '管理首页';
            $this->display();
        } else {
            $this->redirect('Public/login');
        }
    }

    /**
     * 佣金
     */
    public function Commission($agency_id,$fanli)
    {
        $map = array();
       // $agency_id = intval(session('user_auth.agency_id'));

        $all_ids = array();
        if($agency_id){
            $all_ids = all_childs($agency_id);
        }


            $map[] = array('uid'=>array('in',$all_ids));
            $ba_where[] = array('id'=>array('in',$all_ids));

      //昨天的余额
        $yestoday = date("Y-m-d",strtotime("-1 day"));
        $two_yestoday = date("Y-m-d",strtotime("-2 day"));
        $result = M('income')->field('all_bamoney,create_time') ->where(array('uid'=>$agency_id,'date'=>$two_yestoday))->find();
        $ye_all_bamoney = $result['all_bamoney'];

        //昨天充值
        $re_where['c.create_time'] = array('egt',$ye_all_bamoney['create_time']);
        $cur_remoney =  M('recharge')->alias('c')->where($re_where)->sum('money');
        $cur_remoney = empty($cur_remoney) ? 0 : $cur_remoney;
        //提现
        $de_where['c.create_time'] = array('egt',$ye_all_bamoney['create_time']);
        $cur_demoney =  M('deposit')->alias('c')->where($de_where)->sum('money');
        $cur_demoney = empty($cur_demoney) ? 0 : $cur_demoney;
        //账户总余额
        $all_bamoney = M('ucenter_member')->alias('c')->where($ba_where)->sum('money');
        $all_bamoney = empty($all_bamoney) ? 0 : $all_bamoney;

         $c_money =  ($ye_all_bamoney+$cur_remoney - $all_bamoney - $cur_demoney)*$fanli/100;

        $data = array(
            'uid' =>$agency_id,
            'cur_remoney'=>$cur_remoney,
            'cur_demoney'=>$cur_demoney,
            'all_bamoney'=>$all_bamoney,
            'commission'=>$c_money,
            'date'=>date('Y-m-d',strtotime("-1 day")),
            'create_time'=>time()
        );

        if( M('income') ->where(array('uid'=>$agency_id,'date'=>$yestoday))->count()){
            echo '已经添加过了'; 
        }else{
            $result =  M('income')->add($data);
            if($result){
                // 如果主键是自动增长型 成功后返回值就是最新插入的值
                echo  '添加成功';
            }
        }
    }

    //定时执行的方法
    public function crons()
    {
        //在文件中写入内容
        ignore_user_abort(true);
        set_time_limit(0);
        ob_end_clean();
        ob_start();
        //if(date('H:i:s'))
            while (1) {
                ob_flush();
                flush();
                if(date('H') < 1 && date('i')<31) {
                    $select_result = M('member')->field('uid,agency_id,fanli')->select();
                    foreach ($select_result as $val) {
                        if ($val['uid'] != C('USER_ADMINISTRATOR')) {
                            $result = $this->Commission($val['agency_id'],$val['fanli']);
                        }
                    }
                    file_put_contents("test.txt", date("Y-m-d H:i:s") . "执行定时任务,结算！{$result}" . "\r\n<br>", FILE_APPEND);
                }
                file_put_contents("test.txt", date("Y-m-d H:i:s") . "执行！{$result}" . "\r\n<br>", FILE_APPEND);

                usleep(1800000000);
            }

    }


    //定时执行的方法
    public function crons2()
    {
        //在文件中写入内容
        ignore_user_abort(); //即使Client断开(如关掉浏览器)，PHP脚本也可以继续执行.
        set_time_limit(0); // 执行时间为无限制，php默认的执行时间是30秒，通过set_time_limit(0)可以让程序无限制的执行下去
        $interval=60*180; // 每隔5分钟运行
        do{
            if(date('H') < 3) {
                $select_result = M('member')->field('uid,agency_id,fanli')->select();
                foreach ($select_result as $val) {
                    if ($val['uid'] != C('USER_ADMINISTRATOR')) {
                        $result = $this->Commission($val['agency_id'],$val['fanli']);
                    }
                }
                file_put_contents("test.txt", date("Y-m-d H:i:s") . "执行定时任务,结算！{$result}" . "\r\n<br>", FILE_APPEND);
            }
            file_put_contents("test.txt", date("Y-m-d H:i:s") . "执行！{$result}" . "\r\n<br>", FILE_APPEND);

            sleep($interval); // 等待5分钟
        }while(true);

    }

    /**
     * 佣金
     */
    /*
    public function Commission2($agency_id,$fanli)
    {
        $map = array();
        // $agency_id = intval(session('user_auth.agency_id'));

        $all_ids = array();
        if($agency_id){
            $all_ids = all_childs($agency_id);
        }


        $map[] = array('uid'=>array('in',$all_ids));
        $ba_where[] = array('id'=>array('in',$all_ids));

        //前天的余额
        $yestoday = date("Y-m-d",strtotime("-1 day"));
        $two_yestoday = date("Y-m-d",strtotime("-2 day"));
        $result = M('income')->field('all_bamoney,create_time') ->where(array('uid'=>$agency_id,'date'=>$two_yestoday))->find();
        $ye_all_bamoney = $result['all_bamoney'];

        //昨天充值
        $re_where['c.create_time'] = array(array('egt',strtotime(date("Y-m-d",strtotime("-1 day")))),array('elt',strtotime(date("Y-m-d",time()))));
        $re_where['c.uid'] = array('in',$all_ids);
        $cur_remoney =  M('recharge')->alias('c')->where($re_where)->sum('money');
        $cur_remoney = empty($cur_remoney) ? 0 : $cur_remoney;
        //提现
        $de_where['c.create_time'] = array(array('egt',strtotime(date("Y-m-d",strtotime("-1 day")))),array('elt',strtotime(date("Y-m-d",time()))));
        $de_where['c.uid'] = array('in',$all_ids);
        $cur_demoney =  M('deposit')->alias('c')->where($de_where)->sum('money');
        $cur_demoney = empty($cur_demoney) ? 0 : $cur_demoney;
        //账户总余额
        $all_bamoney = M('ucenter_member')->alias('c')->where($ba_where)->sum('money');
        $all_bamoney = empty($all_bamoney) ? 0 : $all_bamoney;

        $c_money =  ($ye_all_bamoney+$cur_remoney - $all_bamoney - $cur_demoney)*$fanli/100;

        $data = array(
            'uid' =>$agency_id,
            'cur_remoney'=>$cur_remoney,//总充值
            'cur_demoney'=>$cur_demoney,//总提现
            'all_bamoney'=>$all_bamoney,//总余额
            'commission'=>$c_money,//总佣金
            'date'=>date('Y-m-d',strtotime("-1 day")),
            'create_time'=>time()
        );

        if( M('income') ->where(array('uid'=>$agency_id,'date'=>$yestoday))->count()){
            echo '已经添加过了';
        }else{
            $result =  M('income')->add($data);
            if($result){
                // 如果主键是自动增长型 成功后返回值就是最新插入的值
                echo  '添加成功';
            }
        }
    }*/

    /**
     * 佣金
     */
    public function Commission3($agency_id,$fanli)
    {
        $map = array();
        // $agency_id = intval(session('user_auth.agency_id'));

        $all_ids = array();
        if($agency_id){
            $all_ids = all_childs($agency_id);
        }


        //总充值
        $re_where['c.uid'] = array('in',$all_ids);
        $re_where['c.is_success'] = 1;
        $cur_remoney =  M('recharge')->alias('c')->where($re_where)->sum('money');
        $cur_remoney = empty($cur_remoney) ? 0 : $cur_remoney;
        //总提现
        $de_where['c.uid'] = array('in',$all_ids);
     //   $de_where['c.is_deposit'] = 1;
        $cur_demoney =  M('deposit')->alias('c')->where($de_where)->sum('money');
        $cur_demoney = empty($cur_demoney) ? 0 : $cur_demoney;
        //账户总余额
        $ba_where['c.id'] = array('in',$all_ids);
        $all_bamoney = M('ucenter_member')->alias('c')->where($ba_where)->sum('money');
        $all_bamoney = empty($all_bamoney) ? 0 : $all_bamoney;

        //客损
        $c_money =  ($cur_remoney - $all_bamoney - $cur_demoney)*$fanli/100;

        $yestoday = date('Y-m-d',strtotime("-1 day"));
        $data = array(
            'uid' =>$agency_id,
            'cur_remoney'=>$cur_remoney,//总充值
            'cur_demoney'=>$cur_demoney,//总提现
            'all_bamoney'=>$all_bamoney,//总余额
            'commission'=>$c_money,//客损
            'date'=>$yestoday,
            'create_time'=>time()
        );

        if( M('income2') ->where(array('uid'=>$agency_id,'date'=>$yestoday))->count()){
            echo '已经添加过了';
        }else{
            $result =  M('income2')->add($data);
            if($result){
                // 如果主键是自动增长型 成功后返回值就是最新插入的值
                echo  '添加成功';
            }
        }
    }
    public function crons3()
    {
        //在文件中写入内容
      //  ignore_user_abort(); //即使Client断开(如关掉浏览器)，PHP脚本也可以继续执行.
        set_time_limit(0); // 执行时间为无限制，php默认的执行时间是30秒，通过set_time_limit(0)可以让程序无限制的执行下去
        $interval=60*60; // 每隔5分钟运行
        do{
            if(date('H') < 3) {
                $select_result = M('member')->field('uid,agency_id,fanli')->select();
                foreach ($select_result as $val) {
                    if ($val['uid'] != C('USER_ADMINISTRATOR')) {
                        $result = $this->Commission3($val['agency_id'],$val['fanli']);
                    }
                }
                file_put_contents("test.txt", date("Y-m-d H:i:s") . "执行定时任务,结算！{$result}" . "\r\n<br>", FILE_APPEND);
            }

            sleep($interval); // 等待5分钟
        }while(true);

    }
}
