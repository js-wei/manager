<?php
namespace app\index\controller;
use QL\QueryList;
use GuzzleHttp;
class Index extends Base{
    public function index(){
    	$list = db('gather')->field('dates',true)->where(['status'=>0])->select();
		p($list);die;
		foreach($list as $k => $v){
			$arr = json_decode($v['rule'],true);
			$result = $this->get_news_list($v['url'],$arr['list']);
			
			foreach($result as $k1 => $v1){
				//p($result);
				p($v1);
				$count = db('article')->where(['title'=>$v1['title']])->count('id');
				if($count){
					continue;
				}
				if($v1['tit']=='[图片]'){
					$json = str_replace('.htm','.hdBigPic.js',$v1['href']);
					
					//实例化一个Http客户端
					$client = new GuzzleHttp\Client();
					
					//发送一个Http请求
					$response = $client->request('GET', $json,[
						'headers' => [
						 	'User-Agent' => 'Mozilla/5.0 (Windows NT 10.0; WOW64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/55.0.2883.87 Safari/537.36 OPR/42.0.2393.94',
					        'Accept'     => 'application/json',
					        'Content-Encoding' => 'gzip, deflate', 
					        'Content-Type' => 'application/javascript;charset=UTF-8',
					    ]
					]);
					
					$response = mb_convert_encoding($response->getBody(), 'UTF-8', 'UTF-8,GBK,GB2312,BIG5');
					//$res = str_replace('/*  |xGv00|d607368ab2e59868b8859771e70f4faf */', '', $response);
					$res = str_replace("'", '"',$res);
					p(json_decode($res,true));die;
				}else{
					$data = $this->get_article($v1['href'],$arr['content']);
					p($data);die;
				}
			}
		}
		
//		foreach($result as $k=>$v){
//			$count = db('article')->where(['title'=>$v['title']])->count('id');
//			if($count){
//				continue;
//			}
//			if($v['tit']!='[图片]'){
//				//$data = $this->get_article($v['href'],$arr['content']);
//				//p($data);die;
//			}else{
//				$json = str_replace('.htm','.hdBigPic.js',$v['href']);
//				//p($json);
//				//实例化一个Http客户端
//				$client = new GuzzleHttp\Client();
//				//$jar = new \GuzzleHttp\Cookie\CookieJar();
//				//发送一个Http请求
//				$response = $client->request('GET', $json,[
//					'headers' => [
//					 	'User-Agent' => 'Mozilla/5.0 (Windows NT 10.0; WOW64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/55.0.2883.87 Safari/537.36 OPR/42.0.2393.94',
//				        'Accept'     => 'application/json',
//				        'Content-Encoding' => 'gzip, deflate', 
//				        'Content-Type' => 'application/javascript;charset=UTF-8',
//				    ]
//				]);
//				
//				$response = mb_convert_encoding($response->getBody(), 'UTF-8', 'UTF-8,GBK,GB2312,BIG5');
//				//$res = str_replace('/*  |xGv00|d607368ab2e59868b8859771e70f4faf */', '', $response);
//				$res = str_replace("'", '"',$res);
//				p(json_decode($res,true));die;
//			}
//		}
		
	}

	protected function get_news_list($url='',$rule=''){
		$url='http://roll.news.qq.com/interface/roll.php?0.25121977164547493&cata=&site=news&date=&page=1&mode=1&of=json';
		$_url=parse_url($url);
		$headers =[
			"User-Agent:Mozilla/5.0 (Windows NT 10.0; WOW64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/55.0.2883.87 Safari/537.36 OPR/42.0.2393.94",
			"Host:{$_url['host']}",
			"Referer:{$_url['scheme']}://{$_url['host']}/"
		];
		
		$return=http($url,'','GET',$headers);

		$rule = [
			'tit'=>['span.t-tit','text'],
			'title'=>['a','text'],
			'href' =>['a','href']
		];
		$return=QueryList::Query($return['data']['article_info'],$rule)->data;
		//p($return);die;
		return $return;
	} 
	/**
	 * @author 魏巍
	 * @description 获取详细的文档
	 */
    protected function get_article($url='',$rule=''){
		$rule =[
			'title' =>['.hd>h1','text'],
		 	'author'=>['span[bosszone="jgname"]','text'],
		    'content' => ['#Cnt-Main-Article-QQ','html'],
		    'date'=>['.a_time','text']
		 ];
		$ql = QueryList::Query($url,$rule,'','UTF-8','GB2312',true)->data;
		return $ql;
	}
}
