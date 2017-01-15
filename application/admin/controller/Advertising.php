<?php
namespace app\admin\controller;

class Advertising extends Base{
	protected function  _initialize(){
		parent::_initialize();
	}

	public function index(){
		$model = [
			'name'=>'广告管理'
		];
		$list = db('carousel')->paginate(10);
		// 查询状态为1的用户数据 并且每页显示10条数据
		$count = db('carousel')->count('*');
		$this->assign('count',$count);
		$this->assign('model',$model);
		$this->assign('list',$list);
		return $this->fetch();
	}

	public function add($id=0){
		$model = [
			'name'=>'广告管理'
		];
		if($id){
        	$vo = db('carousel')->field('dates',true)->find($id);
        	$this->assign('info',$vo);
		}
		$this->assign('model',$model);
		return view();
	}
	/**
	 * [carouseld_handler 修改/添加控制器]
	 * @param integer $id [description]
	 */
	public function carouseld_handler($id=0){
		$param = request()->param();
		if($id){
			$param['dates']=time();
			if(!db('carousel')->update($param)){
				return ['status'=>0,'msg'=>'修改失败请重试'];
			}
			return ['status'=>1,'msg'=>'修改成功','redirect'=>Url('index')];
		}else{
			$param['date']=time();
			if(!db('carousel')->insert($param)){
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
		$_result = $this->_status($id,'carousel',$type);
		return $_result;
	}
}