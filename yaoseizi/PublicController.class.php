<?php
// +----------------------------------------------------------------------
// | OneThink [ WE CAN DO IT JUST THINK IT ]
// +----------------------------------------------------------------------
// | Copyright (c) 2013 http://www.onethink.cn All rights reserved.
// +----------------------------------------------------------------------
// | Author: 麦当苗儿 <zuojiazi@vip.qq.com> <http://www.zjzit.cn>
// +----------------------------------------------------------------------

namespace Home\Controller;


/**
 * 后台首页控制器
 * @author 麦当苗儿 <zuojiazi@vip.qq.com>
 */
class PublicController extends \Think\Controller {

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
          //  echo '已经添加过了';
        }else{
            $result =  M('income2')->add($data);
            if($result){
                // 如果主键是自动增长型 成功后返回值就是最新插入的值
              //  echo  '添加成功';
            }else{
             //   echo json_encode($data);
            }
        }
    }
    public function crons3()
    {
			$select_result = M('member')->field('uid,agency_id,fanli')->select();
			foreach ($select_result as $val) {
				if ($val['uid'] != C('USER_ADMINISTRATOR')) {
					$result = $this->Commission3($val['agency_id'],$val['fanli']);
				}
			\Think\Log::record(date("Y-m-d H:i:s") . "执行定时任务,结算".$val['uid']);
			}
			\Think\Log::record(date("Y-m-d H:i:s") . "执行定时任务,结算".json_encode($select_result));

    }
    public function notify()
    {
        $result = I('post.');
        if($result['bftcode'] == 1000){
            $info = M('recharge')->where('id='.$result['orderNumber'])->find();
            if($info['is_success'] == 0){
                M('recharge')->where('id='.$result['orderNumber'])->save(array('is_success'=>1));
                M('ucenter_member')->where('id='.$info['uid'])->setInc('money',$info['money']);
            }
        }
        echo 'success';exit;
        $return_code = I('get.return_code');
        $channelOrderId = (int)I('get.channelOrderId');
        /*        $result = file_get_contents("php://input");
                 \Think\Log::record(var_export($result,true));
                require VENDOR_PATH.'WebPay.php';
                $pay = new \WebPay();
                //$res_arr = $pay->fromXml($result);
                $res_arr = json_decode($result,true);
                //\Think\Log::record(var_export($res_arr,true));*/
        if($return_code == 0 && $channelOrderId){
            $info = M('recharge')->where('id='.$channelOrderId)->find();
            if($info['is_success'] == 0){
                M('recharge')->where('id='.$channelOrderId)->save(array('is_success'=>1));
                M('ucenter_member')->where('id='.$info['uid'])->setInc('money',$info['money']);
            }

        }
        return true;
    }
}
