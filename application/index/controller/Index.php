<?php
namespace app\index\controller;
use QL\QueryList;
use GuzzleHttp;
class Index extends Base{
    public function index(){
//  	$list = db('gather')->field('dates',true)->find(2);	
//		
//		$url = $list['url'];
//		$arr = json_decode($list['rule'],true);
//		$rule = [
//			'title'=>['.comListBox>h2>a','text'],
//			'href'=>['.comListBox>h2>a','href']
//		];
//		$result = $this->get_news_list($url,$arr['list']);
//		
//		foreach($result as $k1 => $v1){
//			if(!array_key_exists('tit',$v1)){
//				$v1['tit']="军事";
//			}
//			$_count = db('article')->where(['title'=>$v1['title']])->count('*');
//			
//			if(!$_count){
//				$_r=[
//					'title'=>['h1#artical_topic','text'],
//					'author'=>['span.ss03>a','text'],
//					'content'=>['#main_content','html'],
//					'data'=>['span.ss01','text'],
//				];
//				$_result[$k1] = $this->_get_content($v1['tit'],$v1['href'],$_r);
//			}
//		}
//		$_result = array_filter($_result);
//		if(!empty($_result)){
//			if(!$_resuilt = db('article')->insertAll($_result)){
//				
//			}
//		}
		
	}
	
	public function pull(){
		$list = db('gather')->field('dates',true)->where(['status'=>0])->select();
		foreach($list as $k => $v){
			$arr = json_decode($v['rule'],true);
			$result = $this->get_news_list($v['url'],$arr['list']);
			
			foreach($result as $k1 => $v1){
				$article = db('article')->where(['title'=>$v1['title']])->count();
				
				if($article<1){
					if(!array_key_exists('tit',$v1)){
						$v1['tit']=$v['tit'];
					}
					$_result[$k1] = $this->_get_content($v1['tit'],$v1['href'],$arr['content']);
				}
				
				
			}
			$_result = array_filter($_result);
			if(!empty($_result)){
				if(!$_resuilt = db('article')->insertAll($_result)){
					
				}
			}
		}
	}
	/**
	 * 抓取内容
	 */
	public function _get_content($type,$href,$rule){
		$str = preg_replace('/[\[\]\{\}]/','',$type);	//去掉[]{}
		$column = db('column');
		$article = db('article');
		$article='';
		$column_id = $column->field('id,keywords,description')->where(['title'=>$str])->find();
		
		if($str!='图片'){
			$article = $this->get_article($href,$rule);
			
			if(count($article)){
				$_result = [];
				$article = $article[0];
				$article['content']=htmlspecialchars($article['content']);
				$article['column_id']=$column_id['id'];
				$article['keywords']=$article['title']."_".$column_id['keywords'];
				$article['description']=$column_id['description']."_".$column_id['description'];
				#$article['path']=$href;
				#$article['tit']=$type;
				$article['date']=strtotime($article['date']);
			}
		}
		return $article;
	}

	/**
	 * 获取图片
	 */
	public function _get_image($path=''){
		//实例化一个Http客户端
		$client = new GuzzleHttp\Client();
		
		//发送一个Http请求
		$response = $client->request('GET', $path,[
			'headers' => [
			 	'User-Agent' => 'Mozilla/5.0 (Windows NT 10.0; WOW64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/55.0.2883.87 Safari/537.36 OPR/42.0.2393.94',
		        'Accept'     => 'application/json',
		        'Content-Encoding' => 'gzip, deflate', 
		        'Content-Type' => 'application/javascript;charset=UTF-8',
		    ]
		]);
		
		$response = mb_convert_encoding($response->getBody(), 'UTF-8', 'UTF-8,GBK,GB2312,BIG5');
		$res = str_replace('/*  |xGv00|d607368ab2e59868b8859771e70f4faf */', '', $response);
		$res = str_replace("'", '"',$res);
		p(json_decode($res,true));die;
	}
	/**
	 * 得到新闻路径
	 */
	protected function get_news_list($url='',$rule='',$block=''){
		$_url=parse_url($url);
		$headers =[
			"User-Agent:Mozilla/5.0 (Windows NT 10.0; WOW64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/55.0.2883.87 Safari/537.36 OPR/42.0.2393.94",
			"Host:{$_url['host']}",
			"Referer:{$_url['scheme']}://{$_url['host']}/"
		];
		
		$return = http($url,'','GET',$headers);
		
		if(is_array($return)){
			$return = $return['data']['article_info'];
	    }
		
		$return=QueryList::Query($return,$rule,$block)->getData(function($item){
	        return $item;
	    });
		return $return;
	} 
	/**
	 * @author 魏巍
	 * @description 获取详细的文档
	 */
    protected function get_article($url='',$rule='',$block=''){
		$ql = QueryList::Query($url,$rule,$block,'UTF-8','GB2312',true)->data;
		return $ql;
	}
}
