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
 * 前台首页控制器
 * 主要获取首页聚合数据
 */
class IndexController extends HomeController {

    private $resHandler = null;
    private $reqHandler = null;
    private $pay = null;
    private $cfg = null;

	//系统首页
    public function index(){
        //上期没中奖的查一次
		//echo 1;die;
		//dump($this->uid) ;
        $last_info = M('cathectic')->where(array('uid'=>$this->uid,'is_award'=>0))->order('id DESC')->find();
        if($last_info){
            $award_money = 0;
            $last_award = M('award')->where(array('periods'=>$last_info['periods']))->find();
            if($last_award){
                if($last_award['dot'] <= 3){
                    $last_dot = 7;
                }else{
                    $last_dot = 8;
                }
                if($last_info['dot'] == $last_award['dot']){
                    $award_money = $last_info['money']*5;
                }elseif($last_info['dot'] == $last_dot){
                    $award_money = $last_info['money']*1.9;
                }
                if($award_money > 0){
                    M('cathectic')->where(array('uid'=>$this->uid,'periods'=>$last_info['periods']))->save(array(
                        'award_money'=>$award_money,
                        'is_award' => 1
                    ));
                    //中奖直接发放至账户余额
                    M('ucenter_member')->where('id='.$this->uid)->setInc('money',$award_money);
                }
            }


        }
		//echo 1;
        //$res =M('system')->select();
        $member = M('ucenter_member')->where('id='.$this->uid)->find();
        /*/*****************************************上期开奖********************************************/
        $pre_award = M('award')->order('id desc')->field('dot')->limit(12)->select();
        foreach ($pre_award as &$pa){
            if($pa['dot'] > 3){
                $pa['dot'] = '大';
            }else{
                $pa['dot'] = '小';
            }
        }
      $pre_award =array_reverse($pre_award);
        $pre_award_str = implode(',',array_column($pre_award,'dot'));
        $pre_award1 = M('award')->order('id desc')->field('dot')->limit(80)->select();
        foreach ($pre_award1 as &$pa1){
            if($pa1['dot'] > 3){
                $pa1['dot'] =2;
            }else{
                $pa1['dot'] = 1;
            }
        }
     /*   $pre_award1 =array_reverse($pre_award1);*/
        $pre_award_str1 = implode(',',array_column($pre_award1,'dot'));
        $info = M('award')->order('id desc')->find();

        $award_info['pre_periods'] =substr($info['periods'],8);    //上期开奖结果
        $award_info['pre_dot'] = $info['dot'];            //上期开奖号
        if($info['dot'] > 3){
            $award_info['pre_big_dot'] = '大';
        }else{
            $award_info['pre_big_dot'] = '小';
        }
        $pre_time = strtotime($info['periods']);//上期开奖时间
        $nex_time = $pre_time+60;
        $wait_time = $nex_time - time();
        $award_info['wait_time'] = $wait_time;
        //$award_info['nex_periods'] = $award_info['pre_periods'] + 1;
        $award_info['nex_periods'] = date('YmdHi',$nex_time);
        /*/*****************************************滚动公告********************************************/
        $where['type'] = 1;
        $rollinfo = M('Message')->where($where)->order('id desc')->field('content')->find();
        $rollcontent = $rollinfo['content'];        //取出内容

        /*/*****************************************游戏规则********************************************/
        $where['type'] = 2;      //查询type=2的内容字段,倒序取一条
        $gameinfo = M('Message')->where($where)->order('id desc')->field('content')->find();
        $gamecontent = $gameinfo['content'];        //取出内容


        $me = M('cathectic')->where(array('uid'=>$this->uid,'periods'=>$award_info['nex_periods']))->find();
        if($me){
            if($me['dot'] == 7){
                $my['dot'] = '小';
            }elseif($me['dot'] == 8){
                $my['dot'] = '大';
            }else{
                $my['dot'] = $me['dot'];
            }
            $my['money'] = $me['money'];
        }else{
            $my['dot'] = '无';
            $my['money'] = 0;
        }

        $this->assign('pre_award_str',$pre_award_str);
        $this->assign('pre_award_str1',$pre_award_str1);
        $this->assign('gameinfo',$gamecontent);         //游戏规则
        $this->assign('rollinfo',$rollcontent);         //滚动公告
        $this->assign('award_info',$award_info);//客服选择的开奖模式
        $member['money'] = intval($member['money']);
        $this->assign('member',$member);
        $this->assign('my',$my);
        $this->display();
    }



    //历史记录
    public function record(){
        $info = M('award')->order('id desc')->limit(500)->select();
        $this->assign('info',$info);
        $this->display();
    }

    //投注金额和号码
    public function cathecticMoney(){
        //投注用户，是不是要从session中取
        //买的当前期数
        //押的点数（大、小、1，2,3,4,5,6）
        //投注时间
        //投注金额（2,5,10,20,50,100）
        //小于56 大于0 可以投注
        //入库，返回一个投注成功
        M('cathectic')->filter('strip_tags')->add();
    }

    public function ajaxGetAward()
    {
        $info = M('award')->order('id desc')->find();//本期开奖结果

        $me = M('cathectic')->where(array('uid'=>$this->uid,'periods'=>$info['periods']))->find();
        if(!$me){
            $res['status'] = 0;
            $res['msg'] = '本期没有投注，下期加油';
        }else{
            if($me['dot']  < 7){
                //押点数
                if($me['dot'] != $info['dot']){
                    //没中奖
                    $res['status'] = 0;
                    $res['msg'] = '本期未中奖，下期加油';
                }else{
                    $award_money = $me['money'] * 5;
                    $res['status'] = 1;
                    $res['msg'] = "恭喜本期中奖金额为：{$award_money}！，下期继续中大奖！";
                    M('cathectic')->where(array('uid'=>$this->uid,'periods'=>$info['periods']))->save(array(
                        'award_money'=>$award_money,
                        'is_award' => 1
                    ));
                    //中奖直接发放至账户余额
                    M('ucenter_member')->where('id='.$this->uid)->setInc('money',$award_money);
                }
            }else{
                //押大小
                if($info['dot'] <= 3){
                    $award_dot = 7;
                }else{
                    $award_dot = 8;
                }
                if($me['dot'] != $award_dot){
                    //没中奖
                    $res['status'] = 0;
                    $res['msg'] = '本期未中奖，下期加油';
                }else{
                    $award_money = $me['money'] * 1.9;
                    $res['status'] = 1;
                    $res['msg'] = "恭喜本期中奖金额为：{$award_money}！，下期继续中大奖！";
                    M('cathectic')->where(array('uid'=>$this->uid,'periods'=>$info['periods']))->save(array(
                        'award_money'=>$award_money,
                        'is_award' => 1
                    ));
                    //中奖直接发放至账户余额
                    M('ucenter_member')->where('id='.$this->uid)->setInc('money',$award_money);
                }
            }
        }
        $res['dot'] = $info['dot'];
        $res['period'] = $info['periods'];
        $res['res_dot'] = "{$info['dot']}点,".($info['dot'] <= 3 ? '小':'大');
        echo json_encode($res);exit;
    }

    public function ajaxYazhu()
    {
        $dot = (int)I('post.dot');
        $money = (int)I('post.money');
        if($money <= 0){
            $res['status'] = 0;
            $res['msg'] = '金额不正确';
            echo json_encode($res);exit;
        }
        if(!in_array($dot,array(1,2,3,4,5,6,7,8))){
            $res['status'] = 0;
            $res['msg'] = '点数不正确';
            echo json_encode($res);exit;
        }

        $info = M('award')->order('id desc')->find();//本期开奖结果
        $pre_time = strtotime($info['periods']);//上期开奖时间
        $nex_time = $pre_time+60;
        $nex_periods = date('YmdHi',$nex_time);
        $me = M('cathectic')->where(array('uid'=>$this->uid,'periods'=>$nex_periods))->find();
        if($me){
            $res['status'] = 0;
            $res['msg'] = '请勿重复投注';
        }else{
            M()->startTrans();
            $member = M('ucenter_member')->lock(true)->where('id='.$this->uid)->find();
            if($member['money'] < $money){
                $res['status'] = 0;
                $res['msg'] = '账号余额不足，请充值';
                M()->rollback();//回滚
                echo json_encode($res);exit;
            }else{
                M('ucenter_member')->where('id='.$this->uid)->setDec('money',$money);
                M()->commit();//事务提交
            }
            $in_data['uid'] = $this->uid;
            $in_data['periods'] = $nex_periods;
            $in_data['dot'] = $dot;
            $in_data['money'] = $money;
            $in_data['current_date'] = time();
            $res = M('cathectic')->add($in_data);

            //佣金
            if($member['puid'] > 0){
                //直接上级
                /*$is_agency = M('ucenter_member')->where('id='.$member['puid'])->field('puid,is_agency,fanli')->find();
                $is_two_agency = M('ucenter_member')->where('id='.$is_agency['puid'])->field('puid,is_agency,fanli')->find();
                if($is_agency['is_agency']!=1 &&  $is_two_agency['is_agency'] !=1) {*/
                    /* M('ucenter_member')->where('id='.$member['puid'])->setInc('money',$money*($is_agency['fanli']/100));
                     M('money_log')->add(array('uid'=>$member['puid'],'level'=>1,'money'=>$money*0.03,'create_time'=>time()));*/

                    M('ucenter_member')->where('id=' . $member['puid'])->setInc('money', $money * 0.01);
                    M('money_log')->add(array('uid' => $member['puid'], 'level' => 1, 'money' => $money * 0.01, 'create_time' => time()));


                    //再上级
                    $p_1 = M('ucenter_member')->where('id=' . $member['puid'])->getField('puid');
                    if ($p_1 > 0) {
                        M('ucenter_member')->where('id=' . $p_1)->setInc('money', $money * 0.01);
                        M('money_log')->add(array('uid' => $p_1, 'level' => 2, 'money' => $money * 0.01, 'create_time' => time()));
                    }
                    //再上级
                    $p_2 = M('ucenter_member')->where('id=' . $p_1)->getField('puid');
                    if ($p_2 > 0) {
                        M('ucenter_member')->where('id=' . $p_2)->setInc('money', $money * 0.01);
                        M('money_log')->add(array('uid' => $p_2, 'level' => 2, 'money' => $money * 0.01, 'create_time' => time()));
                    }
               // }
            }
            $res['status'] = 1;
            $res['msg'] = '投注成功，请等待开奖!';
        }
        echo json_encode($res);exit;
    }

    public function ajaxRecharge()
    {
		$h = date('H');
        $i = date('i');
        /*if(($h >= '23' && $i >= '30') || ($h >= '00' && $h < '06')){
			$res['status'] = 0;
            $res['msg'] = '平台在23:30-06:00暂停充值';
            echo json_encode($res);exit;
        }*/
        $money = (int)I('post.money');
        if(!in_array($money,array(20,50,100,200,500,1000))){
            $res['status'] = 0;
            $res['msg'] = '金额不正确';
            echo json_encode($res);exit;
        }
		
        $indata['uid'] = $this->uid;
        $indata['money'] = $money;
        $indata['create_time'] = time();
        $id = M('recharge')->add($indata);
        if($id){
          /*  require VENDOR_PATH.'WebPay.php';
            $pay = new \WebPay();
            $values = array(
                'saruLruid'=>'6000000002',
                'transAmt'=>$money*100,
                'out_trade_no'=>"{$id}",
                'body'=>'支付订单',
                'merchantno'=>'私有信息',
                'notify_url'=>'http://weixin.bbxfenxiao.com/yaosezi/php/notify.php',
            );
            $pay->setValues($values);
            $pay->setSign();
            $result = $pay::postXmlCurl($pay->toXml(),'http://123.57.20.120:8050/PhonePospInterface/servlet/WXGalleryPayServlet');
            $res_arr = json_decode($result,true);
            $res['status'] = 1;
            $res['msg'] = $res_arr;
            echo json_encode($res);exit;*/
            /*require_once(VENDOR_PATH.'switpass/Utils.class.php');
            require_once(VENDOR_PATH.'switpass/config/config.php');
            require_once(VENDOR_PATH.'switpass/class/RequestHandler.class.php');
            require_once(VENDOR_PATH.'switpass/class/ClientResponseHandler.class.php');
            require_once(VENDOR_PATH.'switpass/class/PayHttpClient.class.php');
            $this->resHandler = new \ClientResponseHandler();
            $this->reqHandler = new \RequestHandler();
            $this->pay = new \PayHttpClient();
            $this->cfg = new \Config();

            $this->reqHandler->setGateUrl($this->cfg->C('url'));
            $this->reqHandler->setKey($this->cfg->C('key'));

            $this->reqHandler->setParameter('service','pay.weixin.jspay');//接口类型：pay.weixin.jspay
            $this->reqHandler->setParameter('mch_id',$this->cfg->C('mchId'));//必填项，商户号，由威富通分配
            $this->reqHandler->setParameter('version',$this->cfg->C('version'));
            $this->reqHandler->setParameter('out_trade_no',rand(100000,111111111));
            //$this->reqHandler->setParameter('sub_openid','');
            $this->reqHandler->setParameter('total_fee','1');
            $this->reqHandler->setParameter('time_start',time());
            $this->reqHandler->setParameter('limit_credit_pay',1);
            $this->reqHandler->setParameter('body','test');
            $this->reqHandler->setParameter('mch_create_ip',get_client_ip());
            //通知地址，必填项
            $this->reqHandler->setParameter('notify_url','http://weixin.bbxfenxiao.com/yaosezi/php/notify.php');//通知回调地址，目前默认是空格，商户在测试支付和上线时必须改为自己的，且保证外网能访问到
            $this->reqHandler->setParameter('callback_url','http://weixin.bbxfenxiao.com/yaosezi/php');
            $this->reqHandler->setParameter('nonce_str',mt_rand(time(),time()+rand()));//随机字符串，必填项，不长于 32 位
            $this->reqHandler->createSign();//创建签名

            $data = \Utils::toXml($this->reqHandler->getAllParameters());


            $this->pay->setReqContent($this->reqHandler->getGateURL(),$data);
            if($this->pay->call()){
                $this->resHandler->setContent($this->pay->getResContent());
                $this->resHandler->setKey($this->reqHandler->getKey());
                if($this->resHandler->isTenpaySign()){
                    //当返回状态与业务结果都为0时才返回支付二维码，其它结果请查看接口文档
                    if($this->resHandler->getParameter('status') == 0 && $this->resHandler->getParameter('result_code') == 0){
                        echo json_encode(array('status'=>200,'token_id'=>$this->resHandler->getParameter('token_id')));
                        exit();
                    }else{
                        echo json_encode(array('status'=>500,'msg'=>'Error Code:'.$this->resHandler->getParameter('err_code').' Error Message:'.$this->resHandler->getParameter('err_msg')));
                        exit();
                    }
                }
                echo json_encode(array('status'=>500,'msg'=>'Error Code:'.$this->resHandler->getParameter('status').' Error Message:'.$this->resHandler->getParameter('message')));
            }else{
                echo json_encode(array('status'=>500,'msg'=>'Response Code:'.$this->pay->getResponseCode().' Error Info:'.$this->pay->getErrInfo()));
            }*/
           /* $url = 'http://tscand01.nocbi.com/thirdPay/pay/gateway/open';
            $param['appId'] = '1001';
            $param['partnerId'] = '100274';
            $param['channelOrderId'] = $id;
            $param['body'] = 'test';
            $param['totalFee'] = $money*100;
            $param['payType'] = '1600';
            $param['timeStamp'] = time();
            $param['sign'] = strtoupper(md5('appId='.$param['appId'].'&timeStamp='.$param['timeStamp'].'&totalFee='.$param['totalFee'].'&key=2f287e3f701ee92b76fba866d2036c8a'));
            $param['notifyUrl'] = 'http://weixin.bbxfenxiao.com/yaosezi/php/notify.php';
            $param['returnUrl'] = 'http://weixin.bbxfenxiao.com/yaosezi/php';
            $json = $this->curl_get($url.'?'.http_build_query($param));
            $json_arr = json_decode($json,true);
            if($json_arr['return_code'] == 0){
                $res['status'] = 1;
                $res['msg'] = $json_arr['payParam']['pay_info'];
                echo json_encode($res);exit;
            }else{
                $res['status'] = 0;
                $res['msg'] = '支付接口异常';
                echo json_encode($res);exit;
            }*/
            $url="http://api.baifupass.com/topay/bftpay/getcode";


            $signKey="1aa1fce8fcaa9e187fa0a04a81e80029";//签名包key找入网后管理员获取
            $arr['appid']="db7a4ae7";//appid//找管理员
            $arr['trxType']="WX_SCANCODE_JSAPI";//支付形态 微信扫码  WX_SCANCODE  微信收银台  WX_SCANCODE_JSAPI 阿里扫码  Alipay_SCANCODE   阿里收银台 Alipay_SCANCODE_JSAPI
            $arr['amount']= $money;//金额分
            $arr['down_trade_no']=$id;//订单号
            $arr['goodsname']="无";//备注
            $arr['backurl']="http://www.fulaozhongyi.com/notify.php";//回调地址

            $str=TosignHttp($arr);

            $sign=EnTosignHP($str,$signKey);
//echo $str;
            $arr['sign']=$sign;//签名
           // pr($arr);exit;
//exit;
            $t=http_request_json($url,$arr);
           // echo json_encode($t);exit;
            if($t['bftcode'] == 1000){
                $res['status'] = 1;
                $res['msg'] = $t['result']['qrCode'];
                echo json_encode($res);exit;
            }
        }else{
            $res['status'] = 0;
            $res['msg'] = '服务器异常请重试';
            echo json_encode($res);exit;
        }

    }

    public function test11()
    {
        $url="http://api.baifupass.com/topay/bftpay/getcode";


        $signKey="679af17d4fcdaf230f11693a78469bc7";//签名包key找入网后管理员获取
        $arr['appid']="d650580b";//appid//找管理员
        $arr['trxType']="WX_SCANCODE_JSAPI";//支付形态 微信扫码  WX_SCANCODE  微信收银台  WX_SCANCODE_JSAPI 阿里扫码  Alipay_SCANCODE   阿里收银台 Alipay_SCANCODE_JSAPI
        $arr['amount']="0.02";//金额分
        $arr['down_trade_no']=time();//订单号
        $arr['goodsname']="无";//备注
        $arr['backurl']="http://www.fulaozhongyi.com/notify.php";//回调地址

        $str=TosignHttp($arr);

        $sign=EnTosignHP($str,$signKey);
//echo $str;
        $arr['sign']=$sign;//签名
     //  pr($arr);exit;
//exit;
        $t=http_request_json($url,$arr);
        pr($t);
    }

    private function curl_get($url)
    {
        //启动一个CURL会话
        $ch = curl_init();

        // 设置curl允许执行的最长秒数
        curl_setopt($ch, CURLOPT_TIMEOUT, 30);
        curl_setopt($ch,CURLOPT_SSL_VERIFYPEER,false);
        curl_setopt($ch,CURLOPT_SSL_VERIFYHOST,false);
        // 获取的信息以文件流的形式返回，而不是直接输出。
        curl_setopt($ch, CURLOPT_RETURNTRANSFER,1);

        curl_setopt($ch, CURLOPT_URL, $url);

        // 执行操作
        $res = curl_exec($ch);

        curl_close($ch);
        return $res;
    }

    public function wxPay()
    {
        $url = 'https://www.jsmlpay.com/payapi.php/Home/Cmbc/reverse_scan';
        $data['cmbcmchntid'] = 'M18002017020000367633';
        $data['out_order_number'] = 'ML201703121559334307';
        $data['callback'] = 'http://weixin.bbxfenxiao.com/yaosezi/php/notify.php';
        $data['amount'] = 100;
        $data['remark'] = 'fsdfs';
        $res = $this->send($url,$data);
        print_r($res);exit;
    }



    private function send($url, $params = array() , $headers = array()) {
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        if (!empty($params)) {
            //curl_setopt($ch, CURLOPT_POST, true);
           // curl_setopt($ch, CURLOPT_POSTFIELDS, $params);
            $url .= '?'.http_build_query($params);
        }
        if (!empty($headers)) curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        $txt = curl_exec($ch);print_r($txt);exit;
        if (curl_errno($ch)) {
            return false;
        }
        curl_close($ch);
        $ret = json_decode($txt, true);
        if (!$ret) {
            return false;
        }

        return $ret;
    }

}