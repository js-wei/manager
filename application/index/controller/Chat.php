<?php
namespace app\index\controller;
use Workerman\Worker; 
use GatewayClient\Gateway;

class Chat extends Base{
   
   public function index($id=0){
   		return view();
   }
   
   public function bind($client_id=1,$uid=1){
		// 设置GatewayWorker服务的Register服务ip和端口，请根据实际情况改成实际值
		Gateway::$registerAddress = '127.0.0.1:1238';
		// 假设用户已经登录，用户uid和群组id在session中
		$uid      = $_SESSION['uid'];
		$group_id = $_SESSION['group'];
		// client_id与uid绑定
		Gateway::bindUid($client_id, $uid);
		// 加入某个群组（可调用多次加入多个群组）
		Gateway::joinGroup($client_id, $group_id);
   }
   /**
    * 发送信息
    */
   public function send_message($uid=0,$message=''){
		// 设置GatewayWorker服务的Register服务ip和端口，请根据实际情况改成实际值
		Gateway::$registerAddress = '127.0.0.1:1238';
		// 向任意uid的网站页面发送数据
		Gateway::sendToUid($uid, $message);
		// 向任意群组的网站页面发送数据
		Gateway::sendToGroup($group, $message);
   }
}
