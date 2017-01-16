<?php
namespace Service;

class Tianyi{
	public $appId;
	public $appSecret;
	public $redirectUrl;
    public static $RESOURCE_URL=array(
        'authorize'=>'https://oauth.api.189.cn/emp/oauth2/v3/authorize?',
        'token'=>'https://oauth.api.189.cn/emp/oauth2/v3/access_token',
        'logout'=>'https://oauth.api.189.cn/emp/oauth2/v3/logout?',
        'cellphone'=>'http://api.189.cn/upc/real/cellphone_and_province?',
        'age'=>'http://api.189.cn/upc/real/age_and_sex?',
        );   

	public function __construct(){
		$this->appId=C('Open186.AppId');
        $this->appSecret=C('Open186.AppSecret');
        $this->redirectUrl=C('Open186.redirectUrl');
	}
    /**
     * [login 登陆]
     * @return [type] [description]
     */
    public function login(){
    	$params=array(
    		'app_id'=>$this->appId,
    		//'app_secret'=>$this->appSecret,
    		'redirect_uri'=>$this->redirectUrl,
    		'response_type'=>'code',
    		);
    	$query = http_build_query($params);
    	$url = self::$RESOURCE_URL['authorize'].$query;
    	return $url;
    }
    /**
     * [logout 退出登陆]
     * @return [type] [description]
     */
    public function logout(){
        $params=array(
            'app_id'=>$this->appId,
            'redirect_uri'=>C('Open186.logoutRedirect'),
            'access_token'=>$_SESSION['access_token']['access_token'],
            );
        
        $query = http_build_query($params);
        $query = self::$RESOURCE_URL['logout'].$query;

        //p($query);die;
        return $result = $this->curl_get($query);
        //var_dump($result);die;
        //return $this->curl_post(self::$RESOURCE_URL['logout'],$query);
        
    }
    /**
     * [cellphone 获取用户手机]
     * @return [type] [description]
     */
    public function cellphone(){
        $params=array(
            'app_id'=>$this->appId,
            'type'=>'json',
            'access_token'=>$this->get_Access_token(),
            );

        $cellphone = $this->curl_get(self::$RESOURCE_URL['cellphone'].http_build_query($params));
        $attr_age = $this->curl_get(self::$RESOURCE_URL['age'].http_build_query($params));
        return array_merge($cellphone,$attr_age);
    }


    /**
     * [get_user_info 获取用户信息]
     * @return [type] [description]
     */
    public function get_user_info(){
    	$code=$_GET['code'];
    	$open_id=$this->appId;                //$_GET['open_id']
    	if(empty($code)){
    		return false;
    		exit();
    	}

        $access_token=$this->get_Access_token($code);
    	$params=array(
    		'app_id'=>$open_id,
    		'access_token'=>$access_token,
    		'type'=>'json',
    		);
       
    	$query = http_build_query($params);
    	$url = 'http://api.189.cn/upc/usertag/tag2?'.$query;
		return $this->curl_get($url);
    }

    /**
     * [get_Access_token 获取访问令牌]
     * @param  [type] $code [description]
     * @return [type]       [description]
     */
    private function get_Access_token($code){
        $code = !empty($code)?$code:$_GET['code'];
        $params=array(
            'app_id'=>$this->appId,
            'app_secret'=>$this->appSecret,
            'code'=>$code,
            'grant_type'=>'authorization_code',
            'redirect_uri'=>$this->redirectUrl
            );

        ksort($params);
        $send = http_build_query($params);
       
        $access_token = $this->curl_post(self::$RESOURCE_URL['token'],$send);
        $access_token = json_decode($access_token, true);
        session('access_token',$access_token);
        return $access_token['access_token'];

    }
    /**
     * [curl_post post提交]
     * @param  [type] $url  [提交地址]
     * @param  [type] $data [提交数据]
     * @return [type]       [description]
     */
    private function curl_post($url,$data){ // 模拟提交数据函数      
        $curl = curl_init(); // 启动一个CURL会话      
        curl_setopt($curl, CURLOPT_URL, $url); // 要访问的地址                  
        curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, 0); // 对认证证书来源的检查      
        curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, 1); // 从证书中检查SSL加密算法是否存在      
        curl_setopt($curl, CURLOPT_USERAGENT, $_SERVER['HTTP_USER_AGENT']); // 模拟用户使用的浏览器      
        curl_setopt($curl, CURLOPT_FOLLOWLOCATION, 1); // 使用自动跳转      
        curl_setopt($curl, CURLOPT_AUTOREFERER, 1); // 自动设置Referer      
        curl_setopt($curl, CURLOPT_POST, 1); // 发送一个常规的Post请求      
        curl_setopt($curl, CURLOPT_POSTFIELDS, $data); // Post提交的数据包      
        curl_setopt($curl, CURLOPT_COOKIEFILE, $GLOBALS['cookie_file']); // 读取上面所储存的Cookie信息      
        curl_setopt($curl, CURLOPT_TIMEOUT, 30); // 设置超时限制防止死循环      
        curl_setopt($curl, CURLOPT_HEADER, 0); // 显示返回的Header区域内容      
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1); // 获取的信息以文件流的形式返回      
        $tmpInfo = curl_exec($curl); // 执行操作      
        if (curl_errno($curl)) {      
           echo 'Errno'.curl_error($curl);      
        }  
        curl_close($curl); // 关键CURL会话      
        return $tmpInfo; // 返回数据      
    }

    /**
     * 模拟提交参数，支持https提交 可用于各类api请求
     * @param string $url ： 提交的地址
     * @param array $data :POST数组
     * @param string $method : POST/GET，默认GET方式
     * @return mixed
     */
    private function curl_get($url, $data='', $method='GET'){ 
        $curl = curl_init(); // 启动一个CURL会话
        curl_setopt($curl, CURLOPT_URL, $url); // 要访问的地址
        curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false); // 对认证证书来源的检查
        curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, false); // 从证书中检查SSL加密算法是否存在
        curl_setopt($curl, CURLOPT_USERAGENT, $_SERVER['HTTP_USER_AGENT']); // 模拟用户使用的浏览器
        curl_setopt($curl, CURLOPT_FOLLOWLOCATION, 1); // 使用自动跳转
        curl_setopt($curl, CURLOPT_AUTOREFERER, 1); // 自动设置Referer
        if($method=='POST'){
            curl_setopt($curl, CURLOPT_POST, 1); // 发送一个常规的Post请求
            if ($data != ''){
                curl_setopt($curl, CURLOPT_POSTFIELDS, $data); // Post提交的数据包
            }
        }
        curl_setopt($curl, CURLOPT_TIMEOUT, 30); // 设置超时限制防止死循环
        curl_setopt($curl, CURLOPT_HEADER, 0); // 显示返回的Header区域内容
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1); // 获取的信息以文件流的形式返回
        $tmpInfo = curl_exec($curl); // 执行操作
        curl_close($curl); // 关闭CURL会话
        return json_decode($tmpInfo,true); // 返回数据
    }
}