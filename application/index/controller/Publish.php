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

	public function get_colunm($id=0){
		$data = db('column')->field('id,title,name')->where(['status'=>0,'fid'=>0])->order('sort asc')->select();
		if($id==0){
			$data[0]['active']=1;
		}else{
			foreach ($data as $k => $v) {
				if($v['id']==$id){
					$data[$k]['active']=1;
				}else{
					$data[$k]['active']=0;
				}
			}
		}
		return ['status'=>1,'data'=>$data];
	}

<<<<<<< HEAD
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
=======
	public function get_carousel(){
		$data = db('carousel')->field('id,title,description,image,url')->where(['status'=>0])->order('sort asc')->select();
		array_push($data,$data[0]);
		array_unshift($data,$data[count($data)-2]);
		return ['status'=>1,'data'=>$data];
	}

	public function upgred_user_header(){
		$requst = request()->param();
		unset($requst['callback']);
		unset($requst['_']);
		return ['status'=>1,'data'=>$requst];
>>>>>>> 205faae7a1d7d1b717586bfeb814d91e8d3b300f
	}
}