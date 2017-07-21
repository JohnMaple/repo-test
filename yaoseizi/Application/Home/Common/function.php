<?php
// +----------------------------------------------------------------------
// | OneThink [ WE CAN DO IT JUST THINK IT ]
// +----------------------------------------------------------------------
// | Copyright (c) 2013 http://www.onethink.cn All rights reserved.
// +----------------------------------------------------------------------
// | Author: 麦当苗儿 <zuojiazi@vip.qq.com> <http://www.zjzit.cn>
// +----------------------------------------------------------------------




//打印数组或字符串,+1程序会继续执行
function o($value='',$now=0){
    if($now==0) {
        echo '<pre>';
        print_r($value);
        echo '</pre>';
        exit;
    } elseif($now==1) {
        echo '<pre>';
        print_r($value);
        echo '</pre>';
    }
}

/**
 * 前台公共库文件
 * 主要定义前台公共函数库
 */

/**
 * 检测验证码
 * @param  integer $id 验证码ID
 * @return boolean     检测结果
 * @author 麦当苗儿 <zuojiazi@vip.qq.com>
 */
function check_verify($code, $id = 1){
	$verify = new \Think\Verify();
	return $verify->check($code, $id);
}

/**
 * 获取列表总行数
 * @param  string  $category 分类ID
 * @param  integer $status   数据状态
 * @author 麦当苗儿 <zuojiazi@vip.qq.com>
 */
function get_list_count($category, $status = 1){
    static $count;
    if(!isset($count[$category])){
        $count[$category] = D('Document')->listCount($category, $status);
    }
    return $count[$category];
}

/**
 * 获取段落总数
 * @param  string $id 文档ID
 * @return integer    段落总数
 * @author 麦当苗儿 <zuojiazi@vip.qq.com>
 */
function get_part_count($id){
    static $count;
    if(!isset($count[$id])){
        $count[$id] = D('Document')->partCount($id);
    }
    return $count[$id];
}

/**
 * 获取导航URL
 * @param  string $url 导航URL
 * @return string      解析或的url
 * @author 麦当苗儿 <zuojiazi@vip.qq.com>
 */
function get_nav_url($url){
    switch ($url) {
        case 'http://' === substr($url, 0, 7):
        case '#' === substr($url, 0, 1):
            break;        
        default:
            $url = U($url);
            break;
    }
    return $url;
}


function http_request_json($url,$post_data){

    $ch = curl_init();//打开
    $Pt=http_build_query($post_data);
//	exit;
    //exit;
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_POSTFIELDS,$Pt);
    //echo $post_data;
    //exit;
    $response  = curl_exec($ch);
    curl_close($ch);//关闭
    //pr($response);
    //ECHO $response;
    $result = json_decode($response,true);
    return $result;
}

function pr($rs)
{
    echo "<pre>";
    print_r($rs);
    echo "</pre>";


}



/**
 * 数组批量转编码 协议使用
 * @param string $encrypted 解密内容
 * @return array 数组类型的返回结果
 */
function ArrEncode($arr)
{

    foreach($arr as $k=>$v){
        if(is_array($v)){
            $arr[$k] =ArrEncode($v);
        }else{
            if (is_numeric($k))
            {
                unset($arr[$k]);
            }else
            {
                $arr[$k]=urlencode($v);


            }
        }
    }
    return $arr;
}




/**
 * http加密 协议使用
 * @param string $string 解密内容
 * @param string $signkey 秘钥
 * @return string 字符串类型的返回结果
 */

function EnTosignHP($string,$signkey)
{
    $nd=$string."#".$signkey;
    $string=md5($nd);
    //echo "加密前:".$nd."<br>";
    //echo $nd;
    //exit;
    return strtoupper($string);
}


/**
 * http数组拼接 协议使用
 * @param string $ar 解密内容
 * @return string 字符串类型的返回结果
 */

function TosignHttp($ar)
{
    ksort($ar);
    //pr($ar);
    //exit;
    //$arr=ArrEncode($ar);
    //$json=json_encode($arr);


    //$json=urldecode($json);
    $httpcontent="";
    foreach($ar as $key=>$val)
    {
        if($httpcontent=="")
        {

            $httpcontent="#";
        }else
        {

            $httpcontent.="#";
        }

        $httpcontent.=$ar[$key];

    }

    return $httpcontent;

}


/**
 * 数据解码 协议使用
 * @param string $ar 解码内容
 * @return array 数组类型的返回结果
 */
function ArrDecode($arr)
{

    foreach($arr as $k=>$v){
        if(is_array($v)){
            $arr[$k] =ArrDecode($v);
        }else{
            if (is_numeric($k))
            {
                unset($arr[$k]);
            }else
            {
                $arr[$k]=urldecode($v);


            }
        }
    }
    return $arr;
}