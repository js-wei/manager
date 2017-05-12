<?php
namespace app\index\controller;
use think\Loader;

class Pay extends Base{
	
	public function alipay(){//发起支付宝支付
		if(request()->isPost()){
			$Pay = new Pay;
			$result = $Pay->alipay([
				'notify_url' => request()->domain().url('index/index/alipay_notify'),
				'return_url' => '',
				'out_trade_no' => input('post.orderid/s','','trim,strip_tags'),
				'subject' => input('post.subject/s','','trim,strip_tags'),
				'total_fee' => input('post.total_fee/f'),//订单金额，单位为元
				'body' => input('post.body/s','','trim,strip_tags'),
			]);
			if(!$result['code']){
				return $this->error($result['msg']);
			}
			return $result['msg'];
		}
		$this->view->orderid = date("YmdHis").rand(100000,999999);
		return $this->fetch();
	}

	public function alipay_notify()
	{//异步订单通知
		$Pay = new Pay;
		$result = $Pay->notify_alipay();
		exit($result);
	}
    
    /**
     * 支付宝支付
     */
    public function doAlyPay(){
    	Loader::import('alipay.lib.alipay_submit', EXTEND_PATH, '.class.php');
    	//require_once("/extend/alipay/alipay.config.php");
        //require_once("/extend/alipay/lib/alipay_submit.class.php");
        /**************************请求参数**************************/
        //商户订单号，商户网站订单系统中唯一订单号，必填
        $out_trade_no = build_order_no();
        //订单名称，必填
        $subject = '测试的支付';
        //付款金额，必填
        $total_fee = 0.01;  //$order['ordfee']
        //商品描述，可空
        $body = '';
        /************************************************************/
        //构造要请求的参数数组，无需改动
        $parameter = array(
            "service"       	=> "create_direct_pay_by_user",
            "partner"       	=> '2088021699723760',
            "seller_id"  		=> '2088021699723760',
            "payment_type"		=> "1",
            "notify_url"		=> "http://pinkan.cn/Notify/Alipay1",
            "return_url"		=> "http://pinkan.cn/Notify/Alipay",
            "anti_phishing_key"	=> "",
            "exter_invoke_ip"	=> "",
            'qr_pay_mode'		=> 3,
            "out_trade_no"		=> $out_trade_no,
            "subject"			=> $subject,
            "total_fee"			=> $total_fee,
            "body"				=> "$body",
            "_input_charset"	=> trim(strtolower(strtolower('utf-8'))),
            //其他业务参数根据在线开发文档，添加参数.文档地址:https://doc.open.alipay.com/doc2/detail.htm?spm=a219a.7629140.0.0.kiX33I&treeId=62&articleId=103740&docType=1
            "extra_common_param"=> ''
        );
        //建立请求
        $alipaySubmit = new AlipaySubmit($alipay_config);
        $html_text = $alipaySubmit->buildRequestForm($parameter,"get", "确认");
        echo $html_text;
        
    }
    
    
}
