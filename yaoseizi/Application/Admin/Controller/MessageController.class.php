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

//公告/玩法 控制器
class MessageController extends AdminController {

    //显示投注记录
    public function index(){
        $map = $this->messageWhere();

        //查询总记录数 分页
        $tote = M('message')->where($map)->count();

        $page = new \Think\Page($tote,20);
        $show = $page->show();

        $list = M('message')->where($map)->select();

        $this->assign('count',$tote);
        $this->assign('page',$show);
        $this->assign('list',$list);
        $this->display();
    }

    private function messageWhere(){
        $type = I('get.type');          //1公告 2玩法
        $title = I('get.title');        //接收标题
        $t_sta = I('get.time_start');   //接收开始时间
        $t_end = I('get.time_end');     //接收结束时间

        //1公告  2玩法
        if($type == 1){
            $where['type'] = array('eq',1);
        }
        if($type == 2){
            $where['type'] = array('eq',2);
        }

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

    //列表页的详情内容
    public function info(){
        $map['id'] = I('get.id');
        $details = M("message")->where($map)->find();
        $this->assign('details',$details);
        $this->display();
    }

    public function add(){

        //接收表单提交过来的数据，并把数据打包
        $data['title'] = I('post.title');
        $data['content'] = I('post.content');
        $data['type'] = I('post.type');
        $data['current_date'] = I('post.hidden_time');   //时间戳

        if(IS_POST){	//如果表单不为空
            $model = M('message');
            if($model->create($data)){	// 根据表单提交的POST数据创建数据对象
                $result = $model->filter('strip_tags')->add();
                if($result){
                    $this->success("添加成功!",U('index'),1);
                    exit();
                }
            }
            $error = $model->getError();
            $this->error($error);
        }

        $this->display();
    }

    public function edit(){
        $map['id'] = I('get.id');

        //先显示出修改前的信息
        $datalist = M('message')->where($map)->find();

        //取出创建时间字段，格式化，显示在模板页面
        $c_time = date("Y-m-d H:i:s",$data['current_date']);


        //接收表单提交过来的数据，并把数据打包
        $data['title'] = I('post.title');
        $data['content'] = I('post.content');
        $data['type'] = I('post.type');
        $data['current_date'] = I('post.hidden_time');

        if(IS_POST){
            $model = M('message');
            if($model->create($data)){
                if(false !== $model->where($map)->filter('strip_tags')->save()){
                    $this->success("修改成功！",U('index'),1);
                    exit();
                }
            }
            $error = $model->getError();
            $this->error($error);
        }
        $this->assign('time',$c_time);
        $this->assign('data',$datalist);
        $this->display();
    }

    //单条删除删除
    public function del(){

        $model = M('message');
        //如果有id执行删除
        if(false !== $model->delete(I('get.id'))){
            $this->success("删除成功",U('index'));
            exit();
        }else{
            $this->error('删除失败！原因：',$model->getError());
        }

    }

    /*//批量删除
    public function delete(){
        $id = I('post.chk_value');	//接收复选框上带的id

        if(is_array($id)){
            //如果是一个数组，就用,号分割后 作为删除条件
            $where = 'id in('.implode(',',$id).')';
        }

        $fruit = M('message')->where($where)->delete();
        if($fruit){
            $msg = array('error' => 1,'msg' => '批量删除成功');
            $this->ajaxReturn($msg);
        }else{
            $msg = array('error' => 2,'msg' => '批量删除失败');
            $this->ajaxReturn($msg);
        }
    }*/

    private function award(){
        /*/**************************************随机开奖结果*********************************************/
        $year = date("Y",time());   //年
        $month = date("m",time());   //月
        $day = date("d",time());   //日
        $hours = date("H",time());   //时
        $minutes = date("i",time());   //分
        $seconds = date("s",time());   //秒

        $next_period = $hours * 60 + $minutes + 1;     //下一期
        $last_period = $hours * 60 + $minutes;         //上一期

        $period_all = $year.$month.$day.$next_period;   //当前开奖期数：1704220001期
        $timeCount = 59 - $seconds;   //开奖倒计时

        if($timeCount == 56){   //生成开奖号
            $lottery_result_point = mt_rand(1,6);
        }
        $res = array($period_all,$timeCount,$lottery_result_point);
        return $res;
    }

    //自动，手动 模式数据入库
    public function LotteryMode()
    {
       /*
       $award_res = $this->award();    //调用本类中的award开奖方法，获得  开奖期数和倒计时
        $period = $award_res[0];      //开奖期数
        $time = $award_res[1];         //开奖倒计时
        $t = time();                     //当前时间戳
        $res = M('system')->select();    //查询当前模式

        //自动模式【开随机数】
        if ($res[0]['Lottery_mode'] == 1) {
            $num = $award_res[2];       //开奖结果
            if ($num >= 4) {     //如果开的点数是>=4,就是大，dot_big存8
                $dot = 8;
            } else {
                $dot = 7;
            }

            if ($time == 56) {
                $data['periods'] = $period; //期数
                $data['dot'] = $num;            //1-6
                $data['dot_big'] = $dot;        //大或小
                $data['current_date'] = $t;     //开奖时间
                M('award')->add($data);
            }
        }

        //【手动模式1】庄家赢钱模式（买什么号的多，开相反的）

        if ($res[0]['Lottery_mode'] == 2) {
            $where['dot_big'] = 7;      //买小号的订单数
            $where['periods'] = $period;      //当前期
            $min = M('cathectic')->where($where)->field('dot_big')->count();
            $where['dot_big'] = 8;      //买大号的订单数
            $where['periods'] = $period;      //当前期
            $big = M('cathectic')->where($where)->field('dot_big')->count();

            if ($min > $big) {            //如果7>8（买小号的人多）就开大，否则就开小
                $num1 = mt_rand(4, 6);    //开奖结果  随机开大号（4,5,6）
                $dot1 = 8;
            } else if ($big > $min) {
                $num1 = mt_rand(1, 3);    //随机开小号（1,2,3）
                $dot1 = 7;
            } else {                       //如果当期无人购买，开随机号
                $num1 = mt_rand(1, 6);
                if ($num1 >= 4) {
                    $dot1 = 8;
                } else {
                    $dot1 = 7;
                }
            }

            if ($time == 56) {
                $data['periods'] = $period; //期数
                $data['dot'] = $num1;            //1-6
                $data['dot_big'] = $dot1;        //大或小
                $data['current_date'] = $t;     //开奖时间
                $z = M('award')->add($data);
                if ($z) {
                    echo "成功";
                } else {
                    echo "失败";
                }
            }
        }
        //【手动模式2】庄家输钱模式（买什么号的多，开什么号）
        if ($res[0]['Lottery_mode'] == 3) {
            $where['dot_big'] = 7;      //买小号的订单数
            $where['periods'] = $period;      //当前期
            $min = M('cathectic')->where($where)->field('dot_big')->count();
            $where['dot_big'] = 8;      //买大号的订单数
            $where['periods'] = $period;      //当前期
            $big = M('cathectic')->where($where)->field('dot_big')->count();

            if ($min > $big) {            //如果7>8（买小号的人多）就开小
                $num2 = mt_rand(1, 3);
                $dot2 = 7;
            } else if ($big > $min) {      //如果 当期 买大的人多就开大
                $num2 = mt_rand(4, 6);
                $dot2 = 8;
            } else {                       //如果当期无人购买，开随机号
                $num2 = mt_rand(1, 6);
                if ($num2 >= 4) {
                    $dot2 = 8;
                } else {
                    $dot2 = 7;
                }
            }

            if ($time == 56) {
                $data['periods'] = $period; //期数
                $data['dot'] = $num2;            //1-6
                $data['dot_big'] = $dot2;        //大或小
                $data['current_date'] = $t;     //开奖时间
                $z = M('award')->add($data);
                if ($z) {
                    echo "成功";
                } else {
                    echo "失败";
                }
            }
        }
        */
       $sys = M('system')->find();
       $this->assign('sys',$sys);
        $this->display();
    }

    //【2】客服切换模式，跳转到此方法
    public function ajaxModule(){
        $val = I('post.value');
        $res =M('system')->select();
        $data['Lottery_mode'] = $val;

        //如果该字段中有值就更新，没值就添加
        if(!$res){
            $result = M('system')->add($data);
        }else{
            $where['id'] = $res[0]['id'];
            $result = M('system')->where($where)->save($data);
        }
        //选择模式成功，返回提示消息给此模板的ajax
        if($result){
            $data['code'] = 1;
            $data['msg'] = "切换成功";
            echo json_encode($data);
            exit;
        }else{
            $data['code'] = 0;
            $data['msg'] = "切换失败";
            echo json_encode($data);
            exit;
        }

    }

    public function ajaxAward()
    {
        //当前时间期数是否已开奖
        $cur_periods = date('YmdHi',time());
        $cur_time = strtotime($cur_periods);
        $get_award = M('award')->where(array('current_date'=>$cur_time))->find();
        if(!$get_award){
			$lottory_mode = C('LOTTORY_MODE');
			if($lottory_mode == 0){
				 $in_data = array(
					'periods'=>$cur_periods,
					'dot'=>rand(1,6),
					'current_date'=>$cur_time,
				);
				M('award')->add($in_data);
			}else{
				$dot_1 = (int)M('cathectic')->where(array('dot'=>1,'periods'=>$cur_periods))->sum('money');
				$dot_2 = (int)M('cathectic')->where(array('dot'=>2,'periods'=>$cur_periods))->sum('money');
				$dot_3 = (int)M('cathectic')->where(array('dot'=>3,'periods'=>$cur_periods))->sum('money');
				$dot_4 = (int)M('cathectic')->where(array('dot'=>4,'periods'=>$cur_periods))->sum('money');
				$dot_5 = (int)M('cathectic')->where(array('dot'=>5,'periods'=>$cur_periods))->sum('money');
				$dot_6 = (int)M('cathectic')->where(array('dot'=>6,'periods'=>$cur_periods))->sum('money');
				$dot_7 = (int)M('cathectic')->where(array('dot'=>7,'periods'=>$cur_periods))->sum('money');
				$dot_8 = (int)M('cathectic')->where(array('dot'=>8,'periods'=>$cur_periods))->sum('money');
				if($dot_7 > $dot_8){
					//开大
					$a = array('4'=>$dot_4,'5'=>$dot_5,'6'=>$dot_6);
					$dot_res = array_search(min($a), $a);
				}elseif($dot_7 < $dot_8){
					//开小
					$a = array('1'=>$dot_1,'2'=>$dot_2,'3'=>$dot_3);
					$dot_res = array_search(min($a), $a);
				}else{
					$a = array('1'=>$dot_1,'2'=>$dot_2,'3'=>$dot_3,'4'=>$dot_4,'5'=>$dot_5,'6'=>$dot_6);
					$dot_res = array_search(min($a), $a);
					$min_value = $a[$dot_res];
					$rep = array();
					foreach ($a as $k => $v){
						if($v == $min_value){
							$rep[] = $k;
						}
					}
					shuffle($rep);
					$dot_res = array_shift($rep);
				}
				$in_data = array(
					'periods'=>$cur_periods,
					'dot'=>$dot_res,
					'current_date'=>$cur_time,
				);
				M('award')->add($in_data);
			}
            
           /* $res = M('system')->find();    //查询当前模式
            $mode = intval($res['Lottery_mode']);
            switch ($mode){
                case 0:
                    $dot = rand(1,6);
                    if($dot <= 3){
                        $dot_big = 7;
                    }else{
                        $dot_big = 8;
                    }
                    break;
                case 1:

                    break;
                case 2:

                    break;
            }*/
        }

    }

}