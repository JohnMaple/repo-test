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
 * 个人中心控制器
 * 主要获取首页聚合数据
 */
class PersonalController extends HomeController {

	//个人中心首页
    public function index(){
        $member = M('ucenter_member')->where('id='.$this->uid)->find();
 	if($member['is_agency'] == 1){
            header('location:/index.php?s=/Admin/Index/index.html');
        }
        $my_id = str_pad($member['id'],7,'0',STR_PAD_LEFT);
        $this->assign('my_id',$my_id);
        $this->assign('member',$member);
        $this->display();
    }

    //投注记录
    public function cathectic(){
        //查询该用户的投注记录 号码，期数，日期
        $where['uid'] = $this->uid;     //条件
        $lists = M('cathectic')->where($where)->order('id DESC')->select();
        $this->assign('lists',$lists);
        $this->display();
    }

    //提现记录
    public function deposit(){
        $where['uid'] = $this->uid;     //条件
        $lists = M('deposit')->where($where)->order('id DESC')->select();
        $this->assign('lists',$lists);
        $this->display();
    }

    //开奖记录
    public function award(){
        //查询最近30天开奖信息
        $info = M('award')->order('id desc')->limit(30)->select();
        $this->assign('info',$info);
        $this->display();
    }

    public function rechage()
    {
        $money = round(I('post.money'),2);
        if($money <= 0){
            $res['status'] = 0;
            $res['msg'] = '金额不正确';
            echo json_encode($res);exit;
        }
		if($money < 20){
            $res['status'] = 0;
            $res['msg'] = '最低提现金额为20元';
            echo json_encode($res);exit;
        }
		$user = M('ucenter_member')->where('id='.$this->uid)->find();
		if($user['mobile'] == ""){
            $res['status'] = 10;
            $res['msg'] = '请先到我的账户里绑定手机号';
            echo json_encode($res);exit;
        }
        M()->startTrans();
        $member = M('ucenter_member')->lock(true)->where('id='.$this->uid)->find();
        if($member['money'] < $money){
            $res['status'] = 0;
            $res['msg'] = '账号余额不足，请充值';
            M()->rollback();//回滚
            echo json_encode($res);exit;
        }else{
            $today = strtotime(date('Y-m-d',time()));

            $rechage_count = M('deposit')->where(array('create_time'=>array(array('egt',$today),array('lt',$today+86400)),'uid'=>$this->uid))->count('id');
            if($rechage_count >= 2){

                $res['status'] = 0;
                $res['msg'] = '一天只能提现2次';
                M()->rollback();//回滚
                echo json_encode($res);exit;
            }
            M('ucenter_member')->where('id='.$this->uid)->setDec('money',$money);
            M()->commit();//事务提交
            $res['status'] = 1;
           // $res['msg'] = U('Personal/cService2');
            $res['msg'] = U('Personal/index');
            $in_data['uid'] = $this->uid;
            $in_data['money'] = $money;
            $in_data['create_time'] = time();
            $id = M('deposit')->add($in_data);
            //微信提现
//            if($id && !empty($member['wx_openid'])){
//               $rr =  $this->wxRecharge($id,$member['wx_openid'],$money);
//            }
            echo json_encode($res);exit;
        }
    }

    private function wxRecharge($order_id,$wx_openid='',$money=0){
        header('content-type:text/html;charset=utf-8');

        $data['mch_appid']='wxd4ad93659f0d1c90';//商户的应用appid

        $data['mchid']='1380941002';//商户ID

        $data['nonce_str']=$this->unicode();//unicode();//这个据说是唯一的字符串下面有方法

        $data['partner_trade_no']=$order_id;//.time();//这个是订单号。

        $data['openid']=$wx_openid;//这个是授权用户的openid。。这个必须得是用户授权才能用

        $data['check_name']='NO_CHECK';//这个是设置是否检测用户真实姓名的

        $data['re_user_name']='test';//用户的真实名字

        $data['amount']=$money*100;//提现金额

        $data['desc']='服务费';//订单描述

        $data['spbill_create_ip']=$_SERVER['SERVER_ADDR'];//这个最烦了，，还得获取服务器的ip

        $secrect_key='Bbx12345Bbx12345Bbx12345Bbx12345';///这个就是个API密码。32位的。。随便MD5一下就可以了

        $data=array_filter($data);

        ksort($data);

        $str='';

        foreach($data as $k=>$v) {

            $str.=$k.'='.$v.'&';

        }

        $str.='key='.$secrect_key;

        $data['sign']=md5($str);

        $xml=$this->arraytoxml($data);

// echo $xml;

        $url='https://api.mch.weixin.qq.com/mmpaymkttransfers/promotion/transfers';

        $res=$this->curl($xml,$url);

        $return=$this->xmltoarray($res);

        return $return;
    }

    private  function unicode() {

        $str = uniqid(mt_rand(),1);

        $str=sha1($str);

        return md5($str);

    }

    private function arraytoxml($data){

        $str='<xml>';

        foreach($data as $k=>$v) {

            $str.='<'.$k.'>'.$v.'</'.$k.'>';

        }

        $str.='</xml>';

        return $str;

    }

    private  function xmltoarray($xml) {

        //禁止引用外部xml实体

        libxml_disable_entity_loader(true);

        $xmlstring = simplexml_load_string($xml, 'SimpleXMLElement', LIBXML_NOCDATA);

        $val = json_decode(json_encode($xmlstring),true);

        return $val;

    }

    private function curl($param="",$url) {



        $postUrl = $url;

        $curlPost = $param;

        $ch = curl_init();                                      //初始化curl

        curl_setopt($ch, CURLOPT_URL,$postUrl);                 //抓取指定网页

        curl_setopt($ch, CURLOPT_HEADER, 0);                    //设置header

        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);            //要求结果为字符串且输出到屏幕上

        curl_setopt($ch, CURLOPT_POST, 1);                      //post提交方式

        curl_setopt($ch, CURLOPT_POSTFIELDS, $curlPost);           // 增加 HTTP Header（头）里的字段

        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);        // 终止从服务端进行验证

        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, FALSE);

        curl_setopt($ch,CURLOPT_SSLCERT,getcwd().'/ThinkPHP/cert/apiclient_cert.pem'); //这个是证书的位置

        curl_setopt($ch,CURLOPT_SSLKEY,getcwd().'/ThinkPHP/cert/apiclient_key.pem'); //这个也是证书的位置

        $data = curl_exec($ch);                                 //运行curl

        curl_close($ch);

        return $data;

    }

    //佣金记录
    public function commision(){
        $money1 = (float)M('money_log')->where(array('uid'=>$this->uid,'level'=>1))->sum('money');
        $money2 = (float)M('money_log')->where(array('uid'=>$this->uid,'level'=>2))->sum('money');
        $money3 = (float)M('money_log')->where(array('uid'=>$this->uid,'level'=>3))->sum('money');
        $today = strtotime(date('Y-m-d',time()));
        $list = M('money_log')->where(array('uid'=>$this->uid,'create_time'=>array(array('egt',$today),array('lt',$today+86400))))->select();//当天
        $this->assign('money1',$money1);
        $this->assign('money2',$money2);
        $this->assign('money3',$money3);
        $this->assign('list',$list);
        $this->display();
    }

    public function cService2()
    {
        $this->display();
    }

    //联系客服
    public function cService(){

        $this->display();
    }






    public function mobile_num(){
        //获取注册手机最新一条验证码
        $code=I("code");      //手机验证码
        $username=I("mobile");    //获取数据
        $user_table=M('ucenter_member');
            
        $mobile_is_exist=$user_table->where('mobile="'.$username.'"')->select();
        
        if(count($mobile_is_exist) == 0){     //判断手机号是否已注册
            $sms_table=M('sms');
            $sms_rs=$sms_table->where('mobile="'.$username.'"')->order('create_time desc')->find();
            
            //校验验证码
            if($sms_rs['yzm'] == $code){
                $map['id'] = $this->uid;     //条件
                $data["mobile"]=$username;
				//判断是否是首次绑定，首次绑定+6
				$user = $user_table->where('id='.$this->uid)->find();
				if($user['mobile'] == ""){
					$flag=1;
				}else{
					$flag=0;
				}
				//$flag= $user['mobile'] == ""?1:0;
                $res=$user_table->where($map)->save($data);
                if($res){
					if($flag){
						$up_balance=$user_table->where($map)->setInc('money',0);
						$info=array('status'=>1,'info'=>"绑定成功");
					}else{
						$info=array('status'=>1,'info'=>"修改成功");
					}
                    //$info=array('status'=>1,'info'=>"绑定成功");
                }else{
                    $info=array('status'=>0,'info'=>"失败,请重试");
                }
            }else{
                $info=array('status'=>2,'info'=>"验证码错误");
            }
        }else{
			$info=array('status'=>3,'info'=>"手机号已被绑定");
		}
        $this->ajaxReturn($info);
    }




    public function sendsms($mobile='',$content='',$mobileids='',$http=''){
        // $uid = C('mobile_account');
    
        // $pwd = C('mobile_password');
        $mobile=I("mobile");
        $mobile='18850203176';
        $uid = 'baibaoxiang6';
    
        $pwd ='99d9e8973ea5bd604519';

        $yzm=rand(1000,9999);

        $content='本次短信验证码为【'.$yzm.'】';

        $url='http://utf8.sms.webchinese.cn/?Uid='.$uid.'&Key='.$pwd.'&smsMob='.$mobile.'&smsText='.$content;

        $string=$this->get_sms($url);

        if ($string>0) {  //发送成功
            $shuju=array(
                    'mobile'=>$mobile,
                    'create_time'=>NOW_TIME,
                    'yzm'=>$yzm,
            );
            $sms_data['create_time']=NOW_TIME;
            $sms_data['mobile']=$mobile;
            $sms_data['yzm']=$yzm;
             
            $sms_table=M('sms');
            $sms_table->add($sms_data);
            //$this->ajaxReturn($sms_table->getLastSql());exit;
            $data=array(
                    'status'=>1,
                    'info'=>"短信发送成功.".$string,
            );
        }else{
            $data=array(
                    'status'=>0,
                    'info'=>"短信发送失败,请重试.".$string,
            );
        }
        $this->ajaxReturn($data);
    
    }

    public function get_sms($url=''){
    
        if(function_exists('file_get_contents'))
    
        {
    
            $file_contents = file_get_contents($url);
    
        }
    
        else
    
        {
    
            $ch = curl_init();
    
            $timeout = 5;
    
            curl_setopt ($ch, CURLOPT_URL, $url);
    
            curl_setopt ($ch, CURLOPT_RETURNTRANSFER, 1);
    
            curl_setopt ($ch, CURLOPT_CONNECTTIMEOUT, $timeout);
    
            $file_contents = curl_exec($ch);
    
            curl_close($ch);
    
        }
    
        return $file_contents;
    }
}