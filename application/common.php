<?php
// +----------------------------------------------------------------------
// | ThinkPHP [ WE CAN DO IT JUST THINK ]
// +----------------------------------------------------------------------
// | Copyright (c) 2006-2016 http://thinkphp.cn All rights reserved.
// +----------------------------------------------------------------------
// | Licensed ( http://www.apache.org/licenses/LICENSE-2.0 )
// +----------------------------------------------------------------------
// | Author: 流年 <liu21st@gmail.com>
// +----------------------------------------------------------------------

// 应用公共文件





/**
 * 打印函数
 * @param array $array
 */
function p($array){
	dump($array,1,'<pre>',0);	
}
//去除转义字符 
function stripslashes_array(&$array) { 
	while(list($key,$var) = each($array)) { 
		if ($key != 'argc' && $key != 'argv' && (strtoupper($key) != $key || ''.intval($key) == "$key")) { 
			if (is_string($var)) { 
				$array[$key] = stripslashes($var); 
			} 
			if (is_array($var))  { 
				$array[$key] = stripslashes_array($var); 
			} 
		} 
	} 
	return $array; 
}
/**
 * 判断是否存在汉字
 */
function has_chiness($str){
	if(preg_match('/^[\x{4e00}-\x{9fa5}]+$/u', $str)>0){
	 	$flag= true;
	}else if(preg_match('/[\x{4e00}-\x{9fa5}]/u', $str)>0){
	  	$flag= true;
	}else{
		$flag =false;
	}
	return $flag;
}
/**
 *日期转成时间戳
 *含特殊的汉字日期
 */
function strtotime1($str){
	if(has_chiness($str)){
		$result = preg_replace('/([\x80-\xff]*)/i','',$str);	//去掉汉字
		if(strstr($str,'小时前')){
			$d = strtotime('-'.$result.' hour');
			$str = date('Y-m-d H:i:s',$d);
		}else if(strstr($str,'秒前')){
			$d = strtotime('-'.$result.' second');
			$str = date('Y-m-d H:i:s',$d);
		}else if(strstr($str,'分钟前')){
			$d = strtotime('-'.$result.' minute');
			$str = date('Y-m-d H:i:s',$d);
		}else if(strstr($str,'昨天')){
			$d = strtotime('-2 day');
			$str = date('Y-m-d H:i:s',$d);
		}else if(strstr($str,'前天')){
			$d = strtotime('-2 day');
			$str = date('Y-m-d H:i:s',$d);
		}else if(strstr($str,'天前')){
			$d = strtotime('-'.$result.' day');
			$str = date('Y-m-d H:i:s',$d);
		}else if(strstr($str,'月前')){
			$d = strtotime('-'.$result.' months');
			$str = date('Y-m-d H:i:s',$d);
		}else if(strstr($str,'年前')){
			$d = strtotime('-'.$result.' year');
			$str = date('Y-m-d H:i:s',$d);
		}else if(strstr($str,'-')){
			$str = date('Y',time())."-".$str;
		}else{
			$str = preg_replace('/([{年}{月}{日}])/u','/',$str);
		}
	}
	return strtotime($str);
}

/**
 * 浏览器友好的变量输出
 * @param mixed $var 变量
 * @param boolean $echo 是否输出 默认为True 如果为false 则返回输出字符串
 * @param string $label 标签 默认为空
 * @param boolean $strict 是否严谨 默认为true
 * @return void|string
 */
function dump($var, $echo=true, $label=null, $strict=true) {
    $label = ($label === null) ? '' : rtrim($label) . ' ';
    if (!$strict) {
        if (ini_get('html_errors')) {
            $output = print_r($var, true);
            $output = '<pre>' . $label . htmlspecialchars($output, ENT_QUOTES) . '</pre>';
        } else {
            $output = $label . print_r($var, true);
        }
    } else {
        ob_start();
        var_dump($var);
        $output = ob_get_clean();
        if (!extension_loaded('xdebug')) {
            $output = preg_replace('/\]\=\>\n(\s+)/m', '] => ', $output);
            $output = '<pre>' . $label . htmlspecialchars($output, ENT_QUOTES) . '</pre>';
        }
    }
    if ($echo) {
        echo($output);
        return null;
    }else
        return $output;
}
/**
 * @author 魏巍
 * @description 检测邮箱格式
 */
function check_email($email){
	$pattern = "/^([0-9A-Za-z\\-_\\.]+)@([0-9a-z]+\\.[a-z]{2,3}(\\.[a-z]{2})?)$/i";
	if (preg_match($pattern,$email)){
		return true;
	}else{
		return false;
	}
}
/**
 * @author 魏巍
 * @description 计算时间差
 * @param int $start 开始时间戳
 * @param int $end 	 结束时间戳
 */
function time_diff($start,$end){
	$end = time();
	$cha = $end -$start;
 
	$minute=floor($cha/60);
	$hour=floor($cha/60/60);
	$day=floor($cha/60/60/24);
	return [
		'min'=>$minute,
		'hour'=>$hour,
		'day'=>$day
	];
}
/**
 * 密码加密函数
 */
function getEncyptStr($val) {
	vendor('Encrypt');
	return EncryptCopy::encryptByKey ($val,config('ENCRYPT_KEY'));
}
/**
 * 密码解密函数
 */
function getDeEncyptStr($val) {
	vendor('Encrypt');
	return EncryptCopy::decryptByKey ($val,config('ENCRYPT_KEY'));
}
/**
 * @author 魏巍
 * @description 获取当前时间的本周的开始结束时间
 */
function get_first_last_week_day(){
	//当前日期
	$sdefaultDate = date("Y-m-d");
	//$first =1 表示每周星期一为开始日期 0表示每周日为开始日期
	$first=1;
	//获取当前周的第几天 周日是 0 周一到周六是 1 - 6
	$w=date('w',strtotime($sdefaultDate));
	//获取本周开始日期，如果$w是0，则表示周日，减去 6 天
	$week_start=date('Y-m-d',strtotime("$sdefaultDate -".($w ? $w - $first : 6).' days'));
	//本周结束日期
	$week_end=date('Y-m-d',strtotime("$week_start +6 days"));
	
	return [
		'first'=>$week_start,
		'last'=>$week_end
	];
}

/**
 * [split_content 拆分内容]
 * @param  string $content [内容]
 * @return array           [description]
 */
function split_content($content,$separator="，,。"){
	$separator = explode(',', $separator);
	$result =  array();
	$content = htmlspecialchars_decode($content);
	$str = tagstr(trim_all($content));
	$str=str_replace('，',' ',str_replace('。',' ',$str));
	$result = explode(' ',$str);
	$start_index =0; 

	for ($i=0; $i < count($result)-1; $i++){ 

		if($start_index%2==0){
			$_result .= strip_tags($result[$i]).'，';
			$start_index = 1;
		}else{
			$_result .= strip_tags($result[$i]).'。';
			$start_index = 0;
		}
		
	}
	return $_result;
}
/**
 * csv_get_lines 读取CSV文件中的某几行数据
 * @param $csvfile csv文件路径
 * @param $lines 读取行数
 * @param $offset 起始行数
 * @return array
 * */
function csv_get_lines($csvfile, $lines, $offset = 0) {
	if(!$fp = fopen($csvfile, 'r')) {
		return false;
	}
	$i = $j = 0;
	while (false !== ($line = fgets($fp))) {
		if($i++ < $offset) {
			continue; 
		}
		break;
	}
	$data = array();
	while(($j++ < $lines) && !feof($fp)) {
		$data[] = fgetcsv($fp);
	}
	fclose($fp);
	return $data;
}

/**
 * [get_next_split_content 拆分诗词]
 * @param  [type] $content   [诗词]
 * @param  [type] $query     [查询词]
 * @param  string $separator [分隔符]
 * @return [type]            [description]
 */
function get_next_split_content($content,$query,$separator="，,。"){
	
	$separator = explode(',', $separator);
	//p($separator);die;
	$result =  array();
	$content = htmlspecialchars_decode($content);
	$str = tagstr(trim_all($content));
	$str=str_replace('，',' ',str_replace('。',' ',$str));
	$result = explode(' ',$str); 
	$result = $temp = array_filter($result);
	$flag = 0;
	$_result ='';
	for($i=0;$i<count($result);$i++){
		if(strpos($result[$i],$query)!==false){
			$flag = $i;
			break;
		}
	}
	$result = array_splice($result,$flag);
	//开始下标
	$start_index = count($temp) - count($result);

	for ($i=0; $i < count($result); $i++){ 

		if($start_index%2==0){
			$_result .= strip_tags($result[$i]).$separator[0];
			$start_index = 1;
		}else{
			$_result .= strip_tags($result[$i]).$separator[1];
			$start_index = 0;
		}
		
	}
	
	return $_result;
}
/**
 * [filter_mark 去除标点符号]
 * @param  [type] $text [description]
 * @return [type]       [description]
 */
function filter_mark($text){
	if(trim($text)=='') return '';
	$text=preg_replace("/[[:punct:]\s]/",' ',$text);
	$text=urlencode($text);
	$text=preg_replace("/(%7E|%60|%21|%40|%23|%24|%25|%5E|%26|%27|%2A|%28|%29|%2B|%7C|%5C|%3D|\-|_|%5B|%5D|%7D|%7B|%3B|%22|%3A|%3F|%3E|%3C|%2C|\.|%2F|%A3%BF|%A1%B7|%A1%B6|%A1%A2|%A1%A3|%A3%AC|%7D|%A1%B0|%A3%BA|%A3%BB|%A1%AE|%A1%AF|%A1%B1|%A3%FC|%A3%BD|%A1%AA|%A3%A9|%A3%A8|%A1%AD|%A3%A4|%A1%A4|%A3%A1|%E3%80%82|%EF%BC%81|%EF%BC%8C|%EF%BC%9B|%EF%BC%9F|%EF%BC%9A|%E3%80%81|%E2%80%A6%E2%80%A6|%E2%80%9D|%E2%80%9C|%E2%80%98|%E2%80%99|%EF%BD%9E|%EF%BC%8E|%EF%BC%88)+/",' ',$text);
	$text=urldecode($text);
	return trim($text);
} 

/**
 * 去除空格
 * @param $str          字符串
 * @return string       结果
 */
function trim_all($str){
	$q=array(" ","　","\t","\n","\r");
	$h=array("","","","","");
	return str_replace($q,$h,$str);
}
/**
 * [getMacAddr 生成MAC]
 */
function getMacAddr(){
	$return_array = array();
	$temp_array = array();
	$mac_addr = "";
	
	@exec("arp -a",$return_array);
	
	foreach($return_array as $value)
	{
		if(strpos($value,$_SERVER["REMOTE_ADDR"]) !== false &&
		preg_match("/(:?[0-9a-f]{2}[:-]){5}[0-9a-f]{2}/i",$value,$temp_array))
		{
			$mac_addr = $temp_array[0];
			break;
		}
	}
	
	return ($mac_addr);
}

/**
 * [build_order_no 生成订单号]
 * @return [type] [description]
 */
function build_order_no(){
	return date('Ymd').substr(implode(NULL, array_map('ord', str_split(substr(uniqid(), 7, 13), 1))), 0, 8);
}


/**
 * 列出目录下的所有文件
 * @param str $path 目录
 * @param str $exts 后缀
 * @param array $list 路径数组
 * @return array 返回路径数组
 */
function dir_list($path, $exts = '', $list = array()) {
	$path = dir_path($path);
	$files = glob($path . '*');
	foreach($files as $v) {
		if (!$exts || preg_match("/\.($exts)/i", $v)) {
			$list[] = $v;
			if (is_dir($v)) {
				$list = dir_list($v, $exts, $list);
			}
		}
	}
	return $list;
}

/**
 * 组织地址目录
 * @param $path
 * @return mixed|string
 */
function dir_path($path) {
	$path = str_replace('\\', '/', $path);
	if (substr($path, -1) != '/') $path = $path . '/';
	return $path;
}
/**
 * [NoRand 不重复随机数]
 * @param integer $begin [description]
 * @param integer $end   [description]
 * @param integer $limit [description]
 */
function NoRand($begin=0,$end=20,$limit=4){
	$rand_array=range($begin,$end);
	shuffle($rand_array);//调用现成的数组随机排列函数
	return implode('',array_slice($rand_array,0,$limit));//截取前$limit个
}

/**
 * 获取客户端IP地址
 * @param integer $type 返回类型 0 返回IP地址 1 返回IPV4地址数字
 * @param boolean $adv 是否进行高级模式获取（有可能被伪装）
 * @return mixed
 */
function get_client_ip($type = 0,$adv=false) {
	$type       =  $type ? 1 : 0;
	static $ip  =   NULL;
	if ($ip !== NULL) return $ip[$type];
	if($adv){
		if (isset($_SERVER['HTTP_X_FORWARDED_FOR'])) {
			$arr    =   explode(',', $_SERVER['HTTP_X_FORWARDED_FOR']);
			$pos    =   array_search('unknown',$arr);
			if(false !== $pos) unset($arr[$pos]);
			$ip     =   trim($arr[0]);
		}elseif (isset($_SERVER['HTTP_CLIENT_IP'])) {
			$ip     =   $_SERVER['HTTP_CLIENT_IP'];
		}elseif (isset($_SERVER['REMOTE_ADDR'])) {
			$ip     =   $_SERVER['REMOTE_ADDR'];
		}
	}elseif (isset($_SERVER['REMOTE_ADDR'])) {
		$ip     =   $_SERVER['REMOTE_ADDR'];
	}
	// IP地址合法验证
	$long = sprintf("%u",ip2long($ip));
	$ip   = $long ? array($ip, $long) : array('0.0.0.0', 0);
	return $ip[$type];
}



/**
 * 判断黑白天
 * @return bool
 */
function day_or_night(){
	date_default_timezone_set('PRC');   //设定时区，PRC就是天朝
	$hour = date('H');
	if($hour <= 18 && $hour > 6){
		return true;
	}else{
		return false;
	}
}

function unicode_encode ($word){
	$word0 = iconv('gbk', 'utf-8', $word);
	$word1 = iconv('utf-8', 'gbk', $word0);
	$word = ($word1 == $word) ? $word0 : $word;
	$word = json_encode($word);
	$word = preg_replace_callback('/\\\\u(\w{4})/', create_function('$hex', 'return \'&#\'.hexdec($hex[1]).\';\';'), substr($word, 1, strlen($word)-2));
	return $word;
}
function unicode_decode ($uncode){
	$word = json_decode(preg_replace_callback('/&#(\d{5});/', create_function('$dec', 'return \'\\u\'.dechex($dec[1]);'), '"'.$uncode.'"'));
	return $word;
}
/**
 * 检测访问的ip是否为规定的允许的ip
 */
function check_ip($ip=''){
	$ip = $ip?$ip:'0.0.0.0,*.*.*.*,127.0.0.1';
	$allow_ip=explode(',', $ip);
	$ip =get_client_ip();
	$check_ip_arr= explode('.',$ip);
	
	$bl=false;
	foreach ($allow_ip as $val){
		if(($val == $ip) || ($val=='0.0.0.0') || ($val=='*.*.*.*')){
			$bl = true;
		}else if(strpos($val,'*')!==false){
			$arr=[];//
			$arr=explode('.', $val);
			$j=0;
			for($i=0;$i<4;$i++){
				if($arr[$i]!='*'){//不等于*  就要进来检测，如果为*符号替代符就不检查
					if($arr[$i]==$check_ip_arr[$i]){
						$bl=true;
						break;//终止检查本个ip 继续检查下一个ip
					}
				}else{
					$j++;
				}
			}
//			if($j){
//				$bl=true;
//				break;
//			}
		}
	}
	if(!$bl){
		header('HTTP/1.1 403 Forbidden');
		echo "Access forbidden";
		die;
	}
}
/**
 * 转换彩虹字
 * @param string $str
 * @param int $size
 * @param bool $bold
 * @return string
 */
function color_txt($str,$size=20,$bold=false){
	$len = mb_strlen($str);
	$colorTxt   = '';
	if($bold){
		$bold="bolder";
		$bolder="font-weight:".$bold;
	}
	for($i=0; $i<$len; $i++) {
		$colorTxt .=  '<span style="font-size:'.$size.'px;'.$bolder.'; color:'.rand_color().'">'.mb_substr($str,$i,1,'utf-8').'</span>';
	}
	return $colorTxt;
}

function rand_color(){
	return '#'.sprintf("%02X",mt_rand(0,255)).sprintf("%02X",mt_rand(0,255)).sprintf("%02X",mt_rand(0,255));
}
/**
 * 替换表情
 * @param string $content
 * @return string
 */
function replace_phiz($content){
	preg_match_all('/\[.*?\]/is', $content, $arr);
	if($arr[0]){
		$phiz=F('phiz','','./data/');
		foreach ($arr[0] as $v){
			foreach ($phiz as $key =>$value){
				if($v=='['.$value.']'){
					$content=str_repeat($v, '<img src="'.__ROOT__.'/Public/Images/phiz/'.$key.'.gif"/>',$content);
					break;
				}
			}
		}
		return $content;
	}
}
/**
 * 截取字符串
 * @param string $str
 * @param int $start
 * @param int $length
 * @param bool $suffix
 * @param string $charset
 * @return string|string
 */
function sub_str($str,$start=0,$length,$suffix=true,$charset="utf-8"){
	if(strlen($str)==0){
		return ;
	}
	$l=strlen($str);

	if(function_exists("mb_substr"))
		return 	!$suffix?mb_substr($str,$start,$length,$charset):mb_substr($str,$start,$length,$charset)."…";
	else if(function_exists('iconv_substr')){
		return  !$suffix?iconv_substr($str,$start,$length,$charset):iconv_substr($str,$start,$length,$charset)."…";
	}
	$re['utf-8']="/[\x01-\x7f]|[\xc2-\xdf][\x80-\xbf]|[\xe0-\xef][\x80-\xbf]{2}|[\xf0-\xff][\x80-\xbf]{3}/";
	$re['gb2312']="/[\x01-\x7f]|[\xb0-\xf7][\xa0-\xfe]/";
	$re['gbk']="/[\x01-\x7f]|[\x81-\xfe][\x40-\xfe]/";
	$re['big5']="/[\x01-\x7f]|[\x81-\xfe]([\x40-\x7e]|\xa1-\xfe])/";
	preg_match_all($re[$charset],$str,$match);
	$slice = join("",array_slice($match[0],$start,$length));

	if($suffix){
		if($l>$length){
			return $slice."…";
		}else{
			return $slice;
		}
	} 
}
/**
 * [paichu 去掉指定的字符串]
 * @param  [type] $mub [description]
 * @param  [type] $zhi [description]
 * @param  [type] $a   [description]
 * @return [type]      [description]
 */
function paichu($mub,$zhi,$a='l'){
	if(!$mub){
		return "被替换的字符串不存在";
	}

	$mub = mb_convert_encoding($mub,'GB2312','UTF-8');
	$zhi = mb_convert_encoding($zhi,'GB2312','UTF-8');
	 
	if($a==""){
		$last = str_replace($mub,"",$zhi);
	}elseif($a=="r"){
		$last = substr($mub, strrpos($mub,$zhi));
	}elseif($a=="l"){
		//$last = preg_replace("/[\d\D\w\W\s\S]*[".$mub."]+/","",$zhi);
		$last = substr($mub,0,strrpos($mub,$zhi));
	}
	//$last =  mb_convert_encoding($last,'UTF-8','GB2312'); 
	return $last;

}
/**
 * [get_image 获取文档中的图片]
 * @param  [type] $str [文档]
 * @return [type]      [description]
 */
function get_image($str){
	$pattern="/<[img|IMG].*?src=[\'|\"](.*?(?:[\.gif|\.jpg]))[\'|\"].*?[\/]?>/"; 
	preg_match_all($pattern,$str,$match); 
	return $match[1]; 
}
//高亮关键词
function heigLine($key,$content){
	return preg_replace('/'.$key.'/i', '<font color="red"><b>'.$key.'</b></font>', $content);
}

function reg($str){		 
	return  _strip_tags(array("p", "br"),$str); 
}

/**   
* PHP去掉特定的html标签
* @param array $string   
* @param bool $str  
* @return string
*/  
function _strip_tags($tagsArr,$str) {   
	foreach ($tagsArr as $tag) {  
		$p[]="/(<(?:\/".$tag."|".$tag.")[^>]*>)/i";  
	}  
	$return_str = preg_replace($p,"",$str);  
	return $return_str;  
}  
/**
 * [tag 截取字符串]
 * @param  [type] 资源字符串
 * @param  [type] 开始位置
 * @param  [type] 截取长度
 * @return [type] 结果字符串
 */
function tagstr($str,$start=0,$length=250){	
	$str=strip_tags(htmlspecialchars_decode($str));
	$temp=mb_substr($str,$start,$length,'utf-8');
	//return (strlen($str)>$length*1.5)?$temp.'...':$temp;
	return $temp;
}


/**  
 * * 系统邮件发送函数  
 * @param string $to    接收邮件者邮箱  
 * @param string $name  接收邮件者名称  
 * @param string $subject 邮件主题   
 * @param string $body    邮件内容  
 * @param string $attachment 附件列表 
 * @return boolean   
 */ 
function think_send_mail($to, $name, $subject = '', $body = '', $attachment = null){     
	$config = C('THINK_EMAIL');     
	vendor('PHPMailer.class#phpmailer'); 
	vendor('PHPMailer.class#SMTP');   
	$mail             = new \PHPMailer(); //PHPMailer对象     
	$mail->CharSet    = 'UTF-8'; //设定邮件编码，默认ISO-8859-1，如果发中文此项必须设置，否则乱码     
	$mail->IsSMTP();  // 设定使用SMTP服务     
	$mail->SMTPDebug  = 0;                     // 关闭SMTP调试功能,1 = errors and messages,2 = messages only     
	$mail->SMTPAuth   = true;                  // 启用 SMTP 验证功能     
	//$mail->SMTPSecure = 'ssl';                 // 使用安全协议     
	$mail->Host       = $config['SMTP_HOST'];  // SMTP 服务器     
	$mail->Port       = $config['SMTP_PORT'];  // SMTP服务器的端口号     
	$mail->Username   = $config['SMTP_USER'];  // SMTP服务器用户名     
	$mail->Password   = $config['SMTP_PASS'];  // SMTP服务器密码     
	$mail->SetFrom($config['FROM_EMAIL'], $config['FROM_NAME']);     
	$replyEmail       = $config['REPLY_EMAIL']?$config['REPLY_EMAIL']:$config['FROM_EMAIL'];     
	$replyName        = $config['REPLY_NAME']?$config['REPLY_NAME']:$config['FROM_NAME'];     
	$mail->AddReplyTo($replyEmail, $replyName);     
	$mail->Subject    = $subject;     
	$mail->MsgHTML($body);  
	$mail->isHTML(true);   
	$mail->AddAddress($to, $name);  
	//p($mail);die;   
	if(is_array($attachment)){ // 添加附件         
		foreach ($attachment as $file){             
			is_file($file) && $mail->AddAttachment($file);         
		}     
	}     
	return $mail->Send() ? true : $mail->ErrorInfo; 
}
/**
 * [SplitWord 分词]
 * @param [type] $str [description]
 */
function SplitWord($str){
	vendor('SplitWord/SplitWord'); 
	$split=new SplitWord();
	$data=$split->SplitRMM($str);
	$split->Clear();
	return $data;
}


/*
 * 邮件发送
 * @param string $to 收件人邮箱，多个邮箱用,分开
 * @param string $title 标题
 * @param string $content 内容
 */

function send_email($to,$title,$content,$webname="官方网站"){
	
	//邮件相关变量
	$cfg_smtp_server = 'smtp.163.com';
	$cfg_smtp_port = '25';
	$cfg_smtp_usermail = 'jswei30@163.com';	//你的邮箱
	$cfg_smtp_password = 'jswei30';			//你的邮箱密码

	$smtp  = new \Service\smtp($cfg_smtp_server,$cfg_smtp_port,true,$cfg_smtp_usermail,$cfg_smtp_password);
	$smtp->debug = false;
	
	$cfg_webname=$webname;
	$mailtitle=$title;//邮件标题
	$mailbody=$content;//邮件内容 
			//$to 多个邮箱用,分隔
	$mailtype='html';
	return $smtp->sendmail($to,$cfg_webname,$cfg_smtp_usermail, $mailtitle, $mailbody, $mailtype);
}

/**
 * [no_repeat_random 不重复随机数]
 * @param integer $begin [description]
 * @param integer $end   [description]
 * @param integer $limit [description]
 */
function no_repeat_random($begin=0,$end=20,$limit=4){
	$rand_array=range($begin,$end);
	shuffle($rand_array);//调用现成的数组随机排列函数
	return implode('',array_slice($rand_array,0,$limit));//截取前$limit个
}
/**
 * [zeroize 数字补足]
 * @param  int $num    		[带补足数字]
 * @param  int $length 		[补足长度]
 * @param  string $fill   	[补足字符]
 * @param  int $fill   	  	[补足字符]
 * @return [type]         	[description]
 */
function zeroize($num,$length=10,$type=1,$fill='0'){
	$type=$type?STR_PAD_LEFT:STR_PAD_RIGHT;
	return str_pad($num,$length,$fill,$type);
}


//////////////////////////////////////////////////////
//Orderlist数据表，用于保存用户的购买订单记录；
/* Orderlist数据表结构；
CREATE TABLE `tb_orderlist` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `userid` int(11) DEFAULT NULL,购买者userid
  `username` varchar(255) DEFAULT NULL,购买者姓名
  `ordid` varchar(255) DEFAULT NULL,订单号
  `ordtime` int(11) DEFAULT NULL,订单时间
  `productid` int(11) DEFAULT NULL,产品ID
  `ordtitle` varchar(255) DEFAULT NULL,订单标题
  `ordbuynum` int(11) DEFAULT '0',购买数量
  `ordprice` float(10,2) DEFAULT '0.00',产品单价
  `ordfee` float(10,2) DEFAULT '0.00',订单总金额
  `ordstatus` int(11) DEFAULT '0',订单状态
  `payment_type` varchar(255) DEFAULT NULL,支付类型
  `payment_trade_no` varchar(255) DEFAULT NULL,支付接口交易号
  `payment_trade_status` varchar(255) DEFAULT NULL,支付接口返回的交易状态
  `payment_notify_id` varchar(255) DEFAULT NULL,
  `payment_notify_time` varchar(255) DEFAULT NULL,
  `payment_buyer_email` varchar(255) DEFAULT NULL,
  `ordcode` varchar(255) DEFAULT NULL,      
  `isused` int(11) DEFAULT '0',
  `usetime` int(11) DEFAULT NULL,
  `checkuser` int(11) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=5 DEFAULT CHARSET=utf8;
*/
//在线交易订单支付处理函数
//函数功能：根据支付接口传回的数据判断该订单是否已经支付成功；
//返回值：如果订单已经成功支付，返回true，否则返回false；
function checkorderstatus($ordid){
	$ord=M('orderlist');
	$ordstatus=$ord->where('ordid='.$ordid)->getField('ordstatus');
	if($ordstatus==1){
		return true;
	}else{
		return false;    
	}
}
//处理订单函数
//更新订单状态，写入订单支付后返回的数据
function orderhandle($parameter){
	$ordid=$parameter['out_trade_no'];
	$data['payment_trade_no']      =$parameter['trade_no'];
	$data['payment_trade_status']  =$parameter['trade_status'];
	$data['payment_notify_id']     =$parameter['notify_id'];
	$data['payment_notify_time']   =$parameter['notify_time'];
	$data['payment_buyer_email']   =$parameter['buyer_email'];
	$data['ordstatus']             =1;
	$Ord=M('Orderlist');
	$Ord->where('ordid='.$ordid)->save($data);
} 
/*-----------------------------------
2013.8.13更正
下面这个函数，其实不需要，大家可以把他删掉，
具体看我下面的修正补充部分的说明
------------------------------------*/
//获取一个随机且唯一的订单号；
function getordcode(){
	$ord=M('Orderlist');
	$numbers = range (10,99);
	shuffle ($numbers); 
	$code=array_slice($numbers,0,4); 
	$ordcode=$code[0].$code[1].$code[2].$code[3];
	$oldcode=$Ord->where("ordcode='".$ordcode."'")->getField('ordcode');
	if($oldcode){
		getordcode();
	}else{
		return $ordcode;
	}
}

/**
 * [getKey 根据value得到数组key]
 * @param  [type] $arr   [数组]
 * @param  [type] $value [值]
 * @return [type]        [description]
 */
function getKey($arr,$value) {
	if(!is_array($arr)) return null;
		foreach($arr as $k =>$v) {
		  $return = getKey($v, $value);
		  if($v == $value){
			return $k;
		  }
		  if(!is_null($return)){
		   return $return;
		}
	}
}
/**
* Formats a JSON string for pretty printing
*
* @param string $json The JSON to make pretty
* @param bool $html Insert nonbreaking spaces and <br />s for tabs and linebreaks
* @return string The prettified output
*/
function _format_json($json, $html = false) {
	$tabcount = 0;
	$result = '';
	$inquote = false;
	$ignorenext = false;
	if ($html) {
	  	$tab = "   ";
	  	$newline = "<br/>";
	} else {
	  	$tab = "\t";
	  	$newline = "\n";
	}
	for($i = 0; $i < strlen($json); $i++) {
		  $char = $json[$i];
		  if($ignorenext) {
		  	   $result .= $char;
		  	   $ignorenext = false;
		  }else {
			  switch($char) {
			   case '{':
				   $tabcount++;
				   $result .= $char . $newline . str_repeat($tab, $tabcount);
				   break;
			   case '}':
				   $tabcount--;
				   $result = trim($result) . $newline . str_repeat($tab, $tabcount) . $char;
				   break;
			   case ',':
				   $result .= $char . $newline . str_repeat($tab, $tabcount);
				   break;
			   case '"':
				   $inquote = !$inquote;
				   $result .= $char;
				   break;
			   case '\\':
				   if ($inquote) $ignorenext = true;
				   $result .= $char;
			   break;
			   default:
			   		$result .= $char;
			  }
		  }
	}
	return $result;
}