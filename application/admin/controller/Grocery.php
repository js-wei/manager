<?php
namespace app\admin\controller;

class Grocery extends Base{
	protected function  _initialize(){
		parent::_initialize();
	}

	public function index(){
		$model = [
			'name'=>'解忧杂货铺管理'
		];
		$list = db('grocery')->order('id desc')->paginate(10);
		// 查询状态为1的用户数据 并且每页显示10条数据
		$count = db('grocery')->count('*');
		$this->assign('count',$count);
		$this->assign('model',$model);
		$this->assign('list',$list);
		return view();
	}
	
	public function get_possition(){
		get_top_left();
	}
	
	public function add($id=0){
		$model = [
			'name'=>'解忧杂货铺信息'
		];
		if($id){
        	$vo = db('grocery')->field('dates',true)->find($id);
        	$this->assign('info',$vo);
		}
		$this->assign('model',$model);
		return view();
	}
	
	/**
	 * [add_handler 修改/添加控制器]
	 * @param integer $id [description]
	 */
	public function add_handler($id=0){
		$param = request()->param();
		if($id){
			$param['dates']=time();
			$param['isreply']=1;
			if(!db('grocery')->update($param)){
				return ['status'=>0,'msg'=>'修改失败请重试'];
			}
			return ['status'=>1,'msg'=>'回复成功','redirect'=>Url('index')];
		}else{
			$param['date']=time();
			if(!db('grocery')->insert($param)){
				return ['status'=>0,'msg'=>'添加失败请重试'];
			}
			return ['status'=>1,'msg'=>'添加成功','redirect'=>Url('index')];
		}
	}
	
	public function delete_file($u=''){
		if(empty($u)){
			return ['status'=>0,'msg'=>'参数错误'];
		}
		if(!is_file($u)){
			return ['status'=>0,'msg'=>'文件不存在'];
		}
		if(!unlink($u)){
			return ['status'=>0,'msg'=>'删除失败'];
		}
		return ['status'=>1,'msg'=>'删除成功'];
	}
	/**
	 * [status 状态操作]
	 * @param  [type] $id [修改id]
	 * @param  [type] $type  [操作类型]
	 * @return [type]     [description]
	 */
	public function status($id,$type){
		$type = ($type=="delete-all")?"delete":$type;
		$_result = $this->_status($id,'volunteer',$type,'image');
		return $_result;
	}
}