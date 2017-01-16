<?php
namespace app\index\controller;
use QL\QueryList;

class Index extends Base{
    public function index(){
    	$list = db('gather')->field('dates',true)->where(['status'=>0])->select();
		foreach($list as $k => $v){
			$arr = json_decode($v['rule'],true);
			$result = $this->get_news_list($v['url'],$arr['list']);
			foreach($result as $k=>$v){
				$count = db('article')->where(['title'=>$v['title']])->count('id');
				if($count){
					continue;
				}
				if($v['tit']!='[图片]'){
					$data = $this->get_article($v['href'],$arr['content']);
					p($data);die;
				}
			}
			
		}
	}

	protected function get_news_list($url,$rule){
		$headers =[
			'User-Agent:Mozilla/5.0 (Windows NT 10.0; WOW64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/55.0.2883.87 Safari/537.36 OPR/42.0.2393.94',
			'Host:roll.news.qq.com',
			'Referer:http://roll.news.qq.com/'
		];
		
		$return=http($url,'','GET',$headers);

//		$rule = [
//			'tit'=>['span.t-tit','text'],
//			'title'=>['a','text'],
//			'href' =>['a','href']
//		];
		$return=QueryList::Query($return['data']['article_info'],$rule)->data;
		
		return $return;
	} 
	/**
	 * @author 魏巍
	 * @description 获取详细的文档
	 */
    protected function get_article($url,$rule){
//		$rule =[
//			'title' =>['.hd>h1','text'],
//		 	'author'=>['span[bosszone="jgname"]','text'],
//		    'content' => ['#Cnt-Main-Article-QQ','html'],
//		    'date'=>['.a_time','text']
//		 ];
		$ql = QueryList::Query($url,$rule,'','UTF-8','GB2312',true)->data;
		return $ql;
	}
}
