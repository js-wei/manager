<?php
namespace app\admin\controller;

class Member extends Base{
	protected function  _initialize(){
		parent::_initialize();
	}

	public function index(){
		$model = [
			'name'=>'广告管理'
		];
		$list = db('member')->order('id desc')->paginate(10);
		// 查询状态为1的用户数据 并且每页显示10条数据
		$count = db('member')->count('*');
		$this->assign('count',$count);
		$this->assign('model',$model);
		$this->assign('list',$list);
		return view();
	}

	public function add($id=0){
		$model = [
			'name'=>'查看用户信息'
		];
		if($id){
        	$vo = db('member')->field('dates',true)->find($id);
        	$f = $vo['fans']?count(explode(',',$vo['fans'])):0;
        	$this->assign('f',$f);
        	$s = $vo['subscribe']?count(explode(',',$vo['subscribe'])):0;
        	$this->assign('s',$s);
        	$c = $vo['collection']?count(explode(',',$vo['collection'])):0;
        	$this->assign('c',$c);
        	$this->assign('info',$vo);
		}
		$this->assign('model',$model);
		return view();
	}
	
	public function see_user($id=0,$t=0){
		if(!$id){
			echo "参数错误";		
		}
		$vo = db('member')->field('subscribe,fans,collection')->find($id);
		
		if($t==0){
			$member = db('member')->field('id,username,head,nickname')->where(['id'=>['in',$vo['subscribe']]])->select();
			$this->assign('list',$member);
		}else if($t==1){
			$member = db('member')->field('id,username,head,nickname')->where(['id'=>['in',$vo['fans']]])->select();
			$this->assign('list',$member);
		}else{
			$member = db('article')->field('id,title,description')->where(['id'=>['in',$vo['collection']]])->order('id desc')->select();
			$this->assign('list',$member);
		}
		$this->assign('t',$t);
		return $this->fetch();
	}

	/**
	 * [status 状态操作]
	 * @param  [type] $id [修改id]
	 * @param  [type] $type  [操作类型]
	 * @return [type]     [description]
	 */
	public function status($id,$type){
		$type = ($type=="delete-all")?"delete":$type;
		$_result = $this->_status($id,'member',$type,'image');
		return $_result;
	}
}