<?php
namespace app\index\controller;
use QL\QueryList;
class Index extends Base{
    public function index(){
		$this->get_news_list();
	}

	protected function get_news_list(){
		$headers =[
			'User-Agent:Mozilla/5.0 (Windows NT 10.0; WOW64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/55.0.2883.87 Safari/537.36 OPR/42.0.2393.94',
			'Host:roll.news.qq.com',
			'Referer:http://roll.news.qq.com/'
		];
		$url='http://roll.news.qq.com/interface/roll.php?0.25121977164547493&cata=&site=news&date=&page=1&mode=1&of=json';

		$return=http($url,'','GET',$headers);

		$rule = [
			'tit'=>['span.t-tit','text'],
			'href' =>['a','href']
		];
		$return=QueryList::Query($return['data']['article_info'],$rule)->data;
		p($return);die;
		return $return;
	} 
    public function get_article($url=""){
		$rule =[
			'title' =>['.hd>h1','text'],
		 	'author'=>['span[bosszone="jgname"]','text'],
		     'content' => ['#Cnt-Main-Article-QQ','html'],
		     'date'=>['.a_time','text']
		 ];
		$ql = QueryList::Query($url,$rule,'','UTF-8','GB2312',true)->data;
		p($ql);
	}
}
