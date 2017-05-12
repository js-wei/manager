<?php
namespace app\admin\controller;

class Order extends Base{
	protected function  _initialize(){
		parent::_initialize();
	}

	public function index(){
		$model = [
			'name'=>'订单管理'
		];
		$list = db('orderlist')->order('id desc')->paginate(10);
		// 查询状态为1的用户数据 并且每页显示10条数据
		$count = db('orderlist')->count('*');
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
        	$vo = db('orderlist')->field('dates',true)->find($id);
        	$this->assign('info',$vo);
		}
		$this->assign('model',$model);
		return view();
	}
	
	public function set_account($id=0,$p=''){
		$model = [
			'name'=>'查看用户信息'
		];
		if($id){
        	$vo = db('wechat_config')->field('dates',true)->where(['fid'=>$id])->find();
        	$this->assign('info',$vo);
        	$this->assign('fid',$id);
		}
		$this->assign('model',$model);
		return $this->fetch();	
	}
	
	public function see_user($id=0,$t=0){
		if(!$id){
			echo "参数错误";		
		}
		$vo = db('orderlist')->field('subscribe,fans,collection')->find($id);
		
		if($t==0){
			$orderlist = db('orderlist')->field('id,username,head,nickname')->where(['id'=>['in',$vo['subscribe']]])->select();
			$this->assign('list',$orderlist);
		}else if($t==1){
			$orderlist = db('orderlist')->field('id,username,head,nickname')->where(['id'=>['in',$vo['fans']]])->select();
			$this->assign('list',$orderlist);
		}else{
			$orderlist = db('article')->field('id,title,description')->where(['id'=>['in',$vo['collection']]])->order('id desc')->select();
			$this->assign('list',$orderlist);
		}
		$this->assign('t',$t);
		return $this->fetch();
	}
	/**
	 * [add_handler 修改/添加控制器]
	 * @param integer $id [description]
	 */
	public function add_handler($id=0){
		$param = request()->param();
		if($id){
			$param['dates']=time();
			if(!db('orderlist')->update($param)){
				return ['status'=>0,'msg'=>'修改失败请重试'];
			}
			return ['status'=>1,'msg'=>'修改成功','redirect'=>Url('index')];
		}else{
			$param['date']=time();
			if(!db('orderlist')->insert($param)){
				return ['status'=>0,'msg'=>'添加失败请重试'];
			}
			return ['status'=>1,'msg'=>'添加成功','redirect'=>Url('index')];
		}
	}
	
	public function add_account($id=0){
		$param = request()->param();
		if($id){
			$param['dates']=time();
			if(!db('orderlist')->update($param)){
				return ['wechat_config'=>0,'msg'=>'修改失败请重试'];
			}
			return ['status'=>1,'msg'=>'修改成功','redirect'=>Url('index')];
		}else{
			$param['date']=time();
			if(!db('wechat_config')->insert($param)){
				return ['status'=>0,'msg'=>'添加失败请重试'];
			}
			return ['status'=>1,'msg'=>'添加成功','redirect'=>Url('index')];
		}
	}
	/**
	 * [status 状态操作]
	 * @param  [type] $id [修改id]
	 * @param  [type] $type  [操作类型]
	 * @return [type]     [description]
	 */
	public function status($id,$type){
		$type = ($type=="delete-all")?"delete":$type;
		$_result = $this->_status($id,'orderlist',$type,'image');
		return $_result;
	}
}