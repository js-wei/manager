<?php
namespace app\admin\controller;

class article extends Base{
	protected function  _initialize(){
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
		$list = db('article')->where($where)->order('date desc')->paginate(15);
		// 查询状态为1的用户数据 并且每页显示10条数据
		$count = db('article')->count('*');
		$this->assign('count',$count);
		$this->assign('aid',$aid);
		$this->assign('model',$model);
		$this->assign('list',$list);
		return view();
	}
	/**
	 * [add 添加视图]
	 * @param integer $id [description]
	 */
	public function add($aid=0,$id=0){
		$model = [
			'name'=>'文档管理'
		];
		if($id){
        	$vo = db('article')->field('dates',true)->find($id);
        	$this->assign('info',$vo);
		}
		$column = db('column')->field('date',true)->find($aid);
		$list = \Service\Category::LimitForLevel(db('column')->field('id,fid,title')->where('status=0')->select());
		//p($list);die;
		$this->assign('column_list',$list);
		$this->assign('model',$model);
		$this->assign('aid',$aid);
		$this->assign('column',$column);
		return view();
	}
	/**
	 * [articled_handler 修改/添加控制器]
	 * @param integer $id [description]
	 */
	public function add_handler($id=0){
		$param = request()->param();
		$param['content']=htmlspecialchars(input('content'));
		if($param['attr']){
			$attr=$this->makeAttr($param['attr']);	//重置属性
			$param=array_merge($param,$attr);
			unset($param['attr']);
			unset($param['none']);
		}
		unset($param['aid']);
		unset($param['p']);
		
		if($id){
			$param['dates']=time();
			if(!db('article')->update($param)){
				return ['status'=>0,'msg'=>'修改失败请重试'];
			}
			return ['status'=>1,'msg'=>'修改成功','redirect'=>Url('index?aid='.input('aid').'&p='.input('p'))];
		}else{
			$param['date']=time();
			if(!db('article')->insert($param)){
				return ['status'=>0,'msg'=>'添加失败请重试'];
			}
			return ['status'=>1,'msg'=>'添加成功','redirect'=>Url('index?aid='.input('aid').'&p='.input('p'))];
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
		$_result = $this->_status($id,'article',$type,'',Url('index?aid='.input('aid').'&p='.input('p')));
		return $_result;
	}
}