<?php
/**
 * PHP Library for renren.com
 *
 * @author PiscDong (http://www.piscdong.com/)
 */
namespace Service;
class RenApi{

    public function __construct(){
        $access_token=NULL;
        $this->client_id=C('Renren.apiKey');
        $this->client_secret=C('Renren.AppSecret');
        $this->redirectUrl=C('Renren.redirectUrl');
        $this->access_token=$access_token;
    }
 
    public function login_url($callback_url, $scope='',$display='page'){
        $params=array(
            'response_type'=>'code',
            'client_id'=>$this->client_id,
            'redirect_uri'=>$this->redirectUrl,
            'scope'=>$scope,
            'display'=>$display,
            'x_renew' =>true,
        );
        return 'https://graph.renren.com/oauth/authorize?'.http_build_query($params);
    }
 
    public function access_token($callback_url, $code){

        $params=array(
            'grant_type'=>'authorization_code',
            'code'=>$code,
            'client_id'=>$this->client_id,
            'client_secret'=>$this->client_secret,
            'redirect_uri'=>$callback_url
        );
        $url='https://graph.renren.com/oauth/token';
        return $this->http($url, http_build_query($params), 'POST');
    }
 
    public function access_token_refresh($refresh_token){
        $params=array(
            'grant_type'=>'refresh_token',
            'refresh_token'=>$refresh_token,
            'client_id'=>$this->client_id,
            'client_secret'=>$this->client_secret
        );
        $url='https://graph.renren.com/oauth/token';
        return $this->http($url, http_build_query($params), 'POST');
    }
    
    public function me($uid){
        $params=array(
            'client_id'=>$this->client_id,
            'uids'=>$uid,
            'fields'=>''    //name,star,sex,birthday,tinyurl,headurl,mainurl,hometown_location,
            );
        return $this->api('users.getInfo', $params, 'POST');
    }
    
    public function is_login(){

        $renren_t=isset($_SESSION['renren_t'])?$_SESSION['renren_t']:'';
        $renren_id=isset($_SESSION['renren_id'])?$_SESSION['renren_id']:''; 
        //检查是否已登录
        if($renren_t!='' || $renren_id!='' || $_REQUEST['code']){
           return true;
        }else{
           return false;
        }
    }

    public function setStatus($status){
        $params=array(
            'status'=>$status
        );
        return $this->api('status.set', $params, 'POST');
    }
 
    public function getStatus($uid, $count=10, $page=1){
        $params=array(
            'uid'=>$uid,
            'page'=>$page,
            'count'=>$count
        );
        return $this->api('status.gets', $params, 'POST');
    }
 
    public function addBlog($title, $content){
        $params=array(
            'title'=>$title,
            'content'=>$content
        );
        return $this->api('blog.addBlog', $params, 'POST');
    }
 
    public function getBlog($id, $uid){
        $params=array(
            'id'=>$id,
            'uid'=>$uid
        );
        return $this->api('blog.get', $params, 'POST');
    }
 
    public function getComments($id, $uid, $count=10, $page=1){
        $params=array(
            'id'=>$id,
            'uid'=>$uid,
            'page'=>$page,
            'count'=>$count
        );
        return $this->api('blog.getComments', $params, 'POST');
    }
 
    public function api($method_name, $params, $method='GET'){
        $params['method']=$method_name;
        $params['v']='1.0';
        $params['access_token']=$this->access_token;
        $params['format']='json';
        ksort($params);
        $sig_str='';
        foreach($params as $k=>$v)$sig_str.=$k.'='.$v;
        $sig_str.=$this->client_secret;
        $sig=md5($sig_str);
        $params['sig']=$sig;
        $url='http://api.renren.com/restserver.do';
        if($method=='GET'){
            $result=$this->http($url.'?'.http_build_query($params));
        }else{
            $result=$this->http($url, http_build_query($params), 'POST');
        }
        return $result;
    }
 
    public function http($url, $postfields='', $method='GET', $headers=array()){
       
        $ci=curl_init();
        curl_setopt($ci, CURLOPT_SSL_VERIFYPEER, FALSE); 
        curl_setopt($ci, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ci, CURLOPT_CONNECTTIMEOUT, 30);
        curl_setopt($ci, CURLOPT_TIMEOUT, 30);
        if($method=='POST'){
            curl_setopt($ci, CURLOPT_POST, TRUE);
            if($postfields!='')curl_setopt($ci, CURLOPT_POSTFIELDS, $postfields);
        }
        $headers[]="User-Agent: renrenPHP(piscdong.com)";
        curl_setopt($ci, CURLOPT_HTTPHEADER, $headers);
        curl_setopt($ci, CURLOPT_URL, $url);
        $response=curl_exec($ci);
        curl_close($ci);
        $json_r=array();
        if($response!='')$json_r=json_decode($response, true);
        return $json_r;
    }
}
/*
//登录操作
session_start();
require_once('config.php');
require_once('renren.php');
 
$renren_t=isset($_SESSION['renren_t'])?$_SESSION['renren_t']:'';
$renren_id=isset($_SESSION['renren_id'])?$_SESSION['renren_id']:'';
 
//检查是否已登录
if($renren_t!='' || $renren_id!=''){
    $renren=new renrenPHP($renren_k, $renren_s, $renren_t);
 
    //获取登录用户信息
    $result=$renren->me();
    var_dump($result);
 
    
    //access token到期后使用refresh token刷新access token
    //$result=$renren->access_token_refresh($_SESSION['renren_r']);
    //var_dump($result);
   
    
    //发布微博
    //$result=$renren->addBlog('微博标题', '微博内容<br/><img src="http://www.baidu.com/img/baidu_sylogo1.gif">');
    //var_dump($result);
    
 
}else{
    //生成登录链接
    $renren=new renrenPHP($renren_k, $renren_s);
    $login_url=$renren->login_url($callback_url, $scope);
    echo '<a href="',$login_url,'">点击进入授权页面</a>';
}


//callback操作
//授权回调页面，即配置文件中的$callback_url
session_start();
require_once('config.php');
require_once('renren.php');
 
if(isset($_GET['code']) && $_GET['code']!=''){
    $renren=new renrenPHP($renren_k, $renren_s);
    $result=$renren->access_token($callback_url, $_GET['code']);
}
if(isset($result['access_token']) && $result['access_token']!=''){
    echo '授权完成，请记录<br/>access token：<input size="50" value="',$result['access_token'],'"><br/>id：<input size="50" value="',$result['user']['id'],'"><br/>refresh token：<input size="50" value="',$result['refresh_token'],'">';
 
    //保存登录信息，此示例中使用session保存
    $_SESSION['renren_t']=$result['access_token']; //access token
    $_SESSION['renren_id']=$result['user']['id']; //openid
    $_SESSION['renren_r']=$result['refresh_token']; //refresh token
}else{
    echo '授权失败';
}
echo '<br/><a href="./">返回</a>';
*/
