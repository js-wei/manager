<?php
namespace app\admin\controller;

class Single extends Base{
	public function _initialize(){
		parent::_initialize();
	}

	public function index($aid=0){
		$model = [
			'name'=>'文档管理'
		];
		$where='';
		if($aid){
			$where['column_id']=$aid;
		}
		$list = db('article')->where($where)->paginate(10);
		// 查询状态为1的用户数据 并且每页显示10条数据
		$count = db('article')->count('*');
		$this->assign('count',$count);
		$this->assign('aid',$aid);
		$this->assign('model',$model);
		$this->assign('list',$list);
		return view();
	}
}