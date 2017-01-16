<?php
namespace Service;

class BaiduVoice{
	/**
	 * [$rest 语音rest]
	 * @var string
	 */
	private $rest='http://vop.baidu.com/server_api';
	/**
	 * [$auth 授权地址]
	 * @var string
	 */
	private $auth='https://openapi.baidu.com/oauth/2.0/token';
	/**
	 * [$type description]
	 * @var string
	 */
	private $type='pcm';
	/**
	 * [$isJson 是否Json]
	 * @var boolean
	 */
	private $isJson=false;
	/**
	 * [$audio description]
	 * @var string
	 */
	public $audio="http://tsn.baidu.com/text2audio";

	function __construct(){
		$this->appId=C('BaiduVoice.appId');
		$this->apiKey=C('BaiduVoice.apiKey');
		$this->secretKey=C('BaiduVoice.secretKey');
		$this->isJson = C('BaiduVoice.ajax');
	}
	/**
	 * [auth_url 获取access_token]
	 * @return [type] [description]
	 */
	private  function access_token(){
	
		$param = array(
			'grant_type'=>'client_credentials',
			'client_id'=>$this->apiKey,
			'client_secret'=>$this->secretKey,
		);
		$result = json_decode(self::http($this->auth,$param),true);
		return $result['access_token'];
	}
	/**
	 * [Audio2Text TTS文本转换]
	 * @param  [type] $path [文件地址]
	 * @return [type]       [description]
	 */
	public function Audio2Text($path){
		$audio = file_get_contents($path);
		$base_data = base64_encode($audio);
		$array = array(
		        "format" => $this->type,
		        "rate" => 8000,
		        "channel" => 1,
		        "token" => $this->access_token(),
		        "cuid"=> self::guid(),
		        "len" => filesize($path),
		        "speech" => $base_data,
		        );
		$json_array = json_encode($array);
		$content_len = "Content-Length: ".strlen($json_array);
		$header = array ($content_len, 'Content-Type: application/json; charset=utf-8');
		$result = self::get($this->rest,$json_array,$header);
		if($result['err_no']!=0){
			return $result['err_msg'];
			exit;
		}
		return $result['result'];
	}
	/**
	 * [Text2Audio 语音合成]
	 * @param string  $text [文本]
	 * @param string  $lan  [语言]
	 * @param integer $per  [性别：0 为女声，1 为男声]
	 * @param integer $vol  [音量]
	 * @param integer $spd  [语速]
	 * @param integer $pit  [音调]
	 */
	public function Text2Audio($text,$lan='zh',$per=0,$vol=5,$spd=5,$pit=5){
		if(mb_strlen($text,'utf-8')>=1024){
			return '合成文本长度必须小于 1024 字节';
		}
		$array = array(
	        "tex" 	=>  $text,							//合成的文本，使用 UTF-8 编码，请注意文本长度必须小于1024字节 
	        "lan" 	=>  'zh',							//语言选择,填写 zh 
	        "ctp" 	=>  1,								//客户端类型选择，web 端填写 1 
	        "tok" 	=>  $this->access_token(),			//开放平台获取到的开发者 access_token 
	        "cuid"	=>  self::guid(),					//用户唯一标识，用来区分用户
	        "vol" 	=>  $vol,							//音量，取值 0-9，默认为 5 
	        'spd'	=>	$spd,							//语速，取值 0-9，默认为 5 
	        'pit'	=>	$pit,							//音调，取值 0-9，默认为 5
	        "per" 	=>	$per,							//发音人选择，取值 0-1  ；0 为女声，1 为男声，默认为女声
	        );
		
		$result = self::http($this->audio,$array);

		$data = "data:audio/mp3;base64,".chunk_split(base64_encode($result));
		if(is_array($code)){
			$code = json_decode($result,true);
			return $this->err($code['err_no']);
		}
		
		$path ='./Data/Audio/'.date('Y-m-d',time());
		
		if(!file_exists($path)){
			//mkdir($path,0777,true);
			mkdir($path);
			chmod($path,0777);
		}
		$full = $path.'/'.date('Ymdhis').'.mp3';
		file_put_contents($full,$result);
		if($this->isJson){
			header('Content-Type:application/json;charset=utf-8;');
			return json_encode(
				array(
				'status' => 1, 
				'cotent'=>$text,
				'url'=> $full
				));
		}else{
			header('Content-Type:text/html;charset=utf-8;');
			echo '<!DOCTYPE html>
			<html>
			<head>
				<title>语音消息</title>
			</head>
			<body>
				<h4>'.$text.':</h4>
			 	<audio controls="controls" autobuffer="autobuffer" autoplay="autoplay">
					<source src="'.substr($full,1).'"/>
				</audio>
				<a href="'.substr($full,1).'">链接地址</a>
			</body>
			</html> ';
		}
	}

	/**
	 * [guid 获取guid]
	 * @return [type] [description]
	 */
	private static function guid(){
		$charid = strtoupper(md5(uniqid(mt_rand(), true)));
	    $hyphen = chr(45);
	    $uuid = //chr(123).
	    substr($charid, 0, 8).$hyphen
	    .substr($charid, 8, 4).$hyphen
	    .substr($charid,12, 4).$hyphen
	    .substr($charid,16, 4).$hyphen
	    .substr($charid,20,12);
	    //.chr(125);
	    return $uuid;
	}

	/**
	 * [err 显示错误信息]
	 * @param  [type] $code [description]
	 * @return [type]       [description]
	 */
	private function err($code){
		$resCode = array(
			'500' =>  '不支持输入',
			'501' =>  '输入参数不正确', 
			'502' =>  'token验证失败',
			'503' =>  '合成后端错误 '
			);
		return "出现了错误，错误代码：".$code."，错误信息：".$resCode[$code];
	}

	/**
     * 发送HTTP请求方法，目前只支持CURL发送请求
     * @param  string $url    请求URL
     * @param  array  $param  GET参数数组
     * @param  array  $data   POST的数据，GET请求时该参数无效
     * @param  string $method 请求方法GET/POST
     * @return array          响应数据
     */
    protected static function http($url, $param, $data = '', $method = 'GET'){ 
        $opts = array(
            CURLOPT_TIMEOUT        => 30,
            CURLOPT_RETURNTRANSFER => 1,
            CURLOPT_SSL_VERIFYPEER => false,
            CURLOPT_SSL_VERIFYHOST => false,
        );

        /* 根据请求类型设置特定参数 */

        $opts[CURLOPT_URL] = $url . '?' . http_build_query($param);
       
        if(strtoupper($method) == 'POST'){
            $opts[CURLOPT_POST] = 1;
            $opts[CURLOPT_POSTFIELDS] = $data;
            
            if(is_string($data)){ //发送JSON数据
                $opts[CURLOPT_HTTPHEADER] = array(
                    'Content-Type: application/json; charset=utf-8',  
                    'Content-Length: ' . strlen($data),
                );
            }
        }

        /* 初始化并执行curl请求 */
        $ch = curl_init();
        curl_setopt_array($ch, $opts);
        $data  = curl_exec($ch);
        $error = curl_error($ch);
        curl_close($ch);

        //发生错误，抛出异常
        if($error) throw new \Exception('请求发生错误：' . $error);

        return  $data;
    }
    /**
     * [get 请求数据]
     * @param  [type] $url        [description]
     * @param  [type] $json_array [description]
     * @param  [type] $header     [description]
     * @return [type]             [description]
     */
    public static function get($url,$json_array,$header){
    	$ch = curl_init();
		curl_setopt($ch, CURLOPT_URL, $url);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
		curl_setopt($ch, CURLOPT_HTTPHEADER, $header);
		curl_setopt($ch, CURLOPT_POST, 1);
		curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 30);
		curl_setopt($ch, CURLOPT_TIMEOUT, 30);
		curl_setopt($ch, CURLOPT_POSTFIELDS, $json_array);
		$response = curl_exec($ch);
		if(curl_errno($ch)){
		    print curl_error($ch);
		}
		curl_close($ch);
		$response = json_decode($response, true);
		return $response;
    }
}