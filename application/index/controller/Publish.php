<?php
namespace app\index\controller;
use app\index\controller;

class Publish extends Base{
	
	protected function  _initialize(){
		parent::_initialize();
	}

	public function get_site(){
		$data=[
			'title'=>$this->site['title'],
			'logo'=>$this->site['url'].$this->site['logo'],
			'keywords'=>$this->site['keywords'],
			'description'=>$this->site['description'],
			'url'=>$this->site['url'],
		];
		return ['status'=>1,'data'=>$data];
	}

	public function get_colunm(){
		$data = db('column')->field('id,title,name')->where(['status'=>0,'fid'=>0])->order('sort asc')->select();
		return ['status'=>1,'data'=>$data];
	}

	public function get_carousel($l=4){
		$data = db('carousel')->field('id,title,url,description,image')->where(['status'=>0])->order('sort asc')->select();
		$result=[];
		$temp = $data[count($data)-1];
		$temp['image']=$this->site['url'].$temp['image'];
		$temp['duplicate'] = 1;
		$result[0]=$temp;
		foreach ($data as $k => $v) {
			$v['image']= $this->site['url'].$v['image'];
			$v['duplicate'] = 0;
			$result[] = $v;
		}
		$data[0]['image'] = $this->site['url'].$data[0]['image'];
		$data[0]['duplicate'] = 1;
		array_push($result,$data[0]);
		$indicator = count($result)-2;
		return ['status'=>1,'data'=>$result,'indicator'=>$indicator];
	}
}