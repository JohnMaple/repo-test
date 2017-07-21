<?php
// +----------------------------------------------------------------------
// | OneThink [ WE CAN DO IT JUST THINK IT ]
// +----------------------------------------------------------------------
// | Copyright (c) 2013 http://www.onethink.cn All rights reserved.
// +----------------------------------------------------------------------
// | Author: 麦当苗儿 <zuojiazi@vip.qq.com> <http://www.zjzit.cn>
// +----------------------------------------------------------------------

namespace Admin\Controller;
use User\Api\UserApi;

/**
 * 后台用户控制器
 * @author 麦当苗儿 <zuojiazi@vip.qq.com>
 */
class UserController extends AdminController {

    /**
     * 用户管理首页
     * @author 麦当苗儿 <zuojiazi@vip.qq.com>
     */
    public function index(){
        $nickname       =   I('nickname');
        $pid       =   I('get.pid',0,'intval');
        $map['status']  =   array('egt',0);
        $map['uid']  =   array('neq',1);
        $uid = session('user_auth.uid');
        $level = session('user_auth.level');
        if($level >= 2){
            $this->redirect('Admin/Member/index');
        }
        if($pid){
            $map['pid']  =   array('eq',$pid);
        }else{
            $map['pid']  =   array('eq',$uid);
        }


        if(is_numeric($nickname)){
            $map['uid|nickname']=   array(intval($nickname),array('like','%'.$nickname.'%'),'_multi'=>true);
        }else{
            $map['nickname']    =   array('like', '%'.(string)$nickname.'%');
        }

        $list   = $this->lists('Member', $map);
        int_to_string($list);
        $this->assign('_list', $list);
        $this->assign('pid', $pid);
        $this->meta_title = '用户信息';
        $this->display();
    }

    public function kesun()
    {
        $agency_id       =   I('get.agency_id',0,'intval');
        $map['uid']  =   $agency_id;
        $list   = $this->lists('income2', $map);
        $this->assign('_list', $list);
        $this->meta_title = '客损记录';
        $this->display();
    }

    /**
     * 修改昵称初始化
     * @author huajie <banhuajie@163.com>
     */
    public function updateNickname(){
        $nickname = M('Member')->getFieldByUid(UID, 'nickname');
        $this->assign('nickname', $nickname);
        $this->meta_title = '修改昵称';
        $this->display();
    }

    /**
     * 修改昵称提交
     * @author huajie <banhuajie@163.com>
     */
    public function submitNickname(){
        //获取参数
        $nickname = I('post.nickname');
        $password = I('post.password');
        empty($nickname) && $this->error('请输入昵称');
        empty($password) && $this->error('请输入密码');

        //密码验证
        $User   =   new UserApi();
        $uid    =   $User->login(UID, $password, 4);
        ($uid == -2) && $this->error('密码不正确');

        $Member =   D('Member');
        $data   =   $Member->create(array('nickname'=>$nickname));
        if(!$data){
            $this->error($Member->getError());
        }

        $res = $Member->where(array('uid'=>$uid))->save($data);

        if($res){
            $user               =   session('user_auth');
            $user['username']   =   $data['nickname'];
            session('user_auth', $user);
            session('user_auth_sign', data_auth_sign($user));
            $this->success('修改昵称成功！');
        }else{
            $this->error('修改昵称失败！');
        }
    }

    /**
     * 修改密码初始化
     * @author huajie <banhuajie@163.com>
     */
    public function updatePassword(){
        $this->meta_title = '修改密码';
        $this->display();
    }

    /**
     * 修改密码提交
     * @author huajie <banhuajie@163.com>
     */
    public function submitPassword(){
        //获取参数
        $password   =   I('post.old');
        empty($password) && $this->error('请输入原密码');
        $data['password'] = I('post.password');
        empty($data['password']) && $this->error('请输入新密码');
        $repassword = I('post.repassword');
        empty($repassword) && $this->error('请输入确认密码');

        if($data['password'] !== $repassword){
            $this->error('您输入的新密码与确认密码不一致');
        }

        $Api    =   new UserApi();
        $res    =   $Api->updateInfo(UID, $password, $data);
        if($res['status']){
            $this->success('修改密码成功！');
        }else{
            $this->error($res['info']);
        }
    }

    /**
     * 用户行为列表
     * @author huajie <banhuajie@163.com>
     */
    public function action(){
        //获取列表数据
        $Action =   M('Action')->where(array('status'=>array('gt',-1)));
        $list   =   $this->lists($Action);
        int_to_string($list);
        // 记录当前列表页的cookie
        Cookie('__forward__',$_SERVER['REQUEST_URI']);

        $this->assign('_list', $list);
        $this->meta_title = '用户行为';
        $this->display();
    }

    /**
     * 新增行为
     * @author huajie <banhuajie@163.com>
     */
    public function addAction(){
        $this->meta_title = '新增行为';
        $this->assign('data',null);
        $this->display('editaction');
    }

    /**
     * 编辑行为
     * @author huajie <banhuajie@163.com>
     */
    public function editAction(){
        $id = I('get.id');
        empty($id) && $this->error('参数不能为空！');
        $data = M('Action')->field(true)->find($id);

        $this->assign('data',$data);
        $this->meta_title = '编辑行为';
        $this->display();
    }

    /**
     * 更新行为
     * @author huajie <banhuajie@163.com>
     */
    public function saveAction(){
        $res = D('Action')->update();
        if(!$res){
            $this->error(D('Action')->getError());
        }else{
            $this->success($res['id']?'更新成功！':'新增成功！', Cookie('__forward__'));
        }
    }

    /**
     * 会员状态修改
     * @author 朱亚杰 <zhuyajie@topthink.net>
     */
    public function changeStatus($method=null){
        $id = array_unique((array)I('id',0));
        if( in_array(C('USER_ADMINISTRATOR'), $id)){
            $this->error("不允许对超级管理员执行该操作!");
        }
        $id = is_array($id) ? implode(',',$id) : $id;
        if ( empty($id) ) {
            $this->error('请选择要操作的数据!');
        }
        $map['uid'] =   array('in',$id);
        switch ( strtolower($method) ){
            case 'forbiduser':
                $this->forbid('Member', $map );
                break;
            case 'resumeuser':
                $this->resume('Member', $map );
                break;
            case 'deleteuser':
                $info = M('member')->where($map)->find();
                if($info['agency_id']){
                    M('ucenter_member')->where('id='.$info['agency_id'])->save(array('is_agency'=>0,'fanli'=>0,'pid'=>0));
                }
                M('member')->where($map)->delete();
                $map['id'] =   array('in',$id);
                M('ucenter_member')->where($map)->delete();
                $this->success('操作成功');
                break;
            default:
                $this->error('参数非法');
        }
    }
    public function edit(){

        if(IS_POST){
            $id = I('id');
            $password = I('password');
            $repassword = I('repassword');
            $username = I('username');
            $email = I('email');
            $agency_id = $username;
            $fanli = I('fanli');
            if(!is_administrator()){
                //给下级开代理先判断是否为他的下级
                if($agency_id){
                    $p_agency_id = session('user_auth.agency_id');
                    if(M('ucenter_member')->where('id='.$agency_id)->getField('puid') != $p_agency_id){
                        $this->error('只能给自己的直接下级开代理！');
                    }
                }
            }
            /* 检测密码 */
            if(!empty($password)){
                if($password != $repassword){
                    $this->error('密码和重复密码不一致！');
                }
                $password = think_ucenter_md5($password, UC_AUTH_KEY);
                M('ucenter_member')->where('id='.$id)->save(array('password'=>$password,'is_agency'=>0,'fanli'=>0,'pid'=>0));
            }
            $info = M('member')->where('uid='.$id)->find();
            if($info['agency_id'] != $agency_id){
                M('ucenter_member')->where('id='.$info['agency_id'] )->save(array('is_agency'=>0,'fanli'=>0,'pid'=>0));
            }
            if($agency_id){
                M('ucenter_member')->where('id='.$agency_id)->save(array('is_agency'=>1,'fanli'=>$fanli,'pid'=>session('user_auth.uid')));
            }
            M('member')->where('uid='.$id)->save(array('agency_id'=>$agency_id,'fanli'=>$fanli,'pid'=>session('user_auth.uid'),'level'=>session('user_auth.level')+1));
            $this->success('修改成功！',U('index'));
        } else {
            $this->meta_title = '编辑用户';
            $id = I('get.id');
            $info = M('member')->where('uid='.$id)->find();
            $this->assign('info',$info);
            $this->display();
        }
    }

    public function add($username = '', $password = '', $repassword = '', $email = '',$fanli = ''){
        if(IS_POST){
			$agency_id = $username;
            if(!is_administrator()){
                //给下级开代理先判断是否为他的下级
                if($agency_id){
                    $p_agency_id = session('user_auth.agency_id');
                    if(M('ucenter_member')->where('id='.$agency_id)->getField('puid') != $p_agency_id){
                        $this->error('只能给自己的直接下级开代理！');
                    }
                }
            }
            /* 检测密码 */
            if($password != $repassword){
                $this->error('密码和重复密码不一致！');
            }

            /* 调用注册接口注册用户 */
            $User   =   new UserApi;
            $uid    =   $User->register($username, $password, $email);
            if(0 < $uid){ //注册成功
                $user = array('uid' => $uid, 'nickname' => $username, 'status' => 1,'agency_id'=>intval($agency_id),'fanli'=>intval($fanli),'pid'=>session('user_auth.uid'),'level'=>session('user_auth.level')+1);
                if($agency_id){
                    M('ucenter_member')->where('id='.$agency_id)->save(array('is_agency'=>1,'fanli'=>$fanli,'pid'=>session('user_auth.uid')));
                }
                if(!M('Member')->add($user)){
                    $this->error('用户添加失败！');
                } else {
                    $this->success('用户添加成功！',U('index'));
                }
            } else { //注册失败，显示错误信息
                $this->error($this->showRegError($uid));
            }
        } else {
            $this->meta_title = '新增用户';
            $this->display();
        }
    }

    /**
     * 获取用户注册错误信息
     * @param  integer $code 错误编码
     * @return string        错误信息
     */
    private function showRegError($code = 0){
        switch ($code) {
            case -1:  $error = '用户名长度必须在16个字符以内！'; break;
            case -2:  $error = '用户名被禁止注册！'; break;
            case -3:  $error = '用户名被占用！'; break;
            case -4:  $error = '密码长度必须在6-30个字符之间！'; break;
            case -5:  $error = '邮箱格式不正确！'; break;
            case -6:  $error = '邮箱长度必须在1-32个字符之间！'; break;
            case -7:  $error = '邮箱被禁止注册！'; break;
            case -8:  $error = '邮箱被占用！'; break;
            case -9:  $error = '手机格式不正确！'; break;
            case -10: $error = '手机被禁止注册！'; break;
            case -11: $error = '手机号被占用！'; break;
            default:  $error = '未知错误';
        }
        return $error;
    }

}
