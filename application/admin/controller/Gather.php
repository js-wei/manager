<?php
namespace app\admin\controller;

class Gather extends Base{
	
	public function _initialize(){
		parent::_initialize();
	}
	public function index(){
		$model = ['name'=>'文章采集'];
		$list = db('gather')->paginate(10);
		// 查询状态为1的用户数据 并且每页显示10条数据
		$count = db('gather')->count('*');
		$this->assign('count',$count);
		$this->assign('model',$model);
		$this->assign('list',$list);
		return view();
	}
	public function add($id=0){
		$model = [
			'name'=>'文章采集'
		];
		$type = 0;
		if($id){
        	$vo = db('gather')->field('dates',true)->find($id);
        	$this->assign('info',$vo);
			$vo['rule'] = json_decode($vo['rule'],true);
			$list = $vo['rule']['list'];
			$content = $vo['rule']['content'];
			$this->assign('list',$list);
			$this->assign('content',$content);
			if(!empty($list)){
				$type = 1;
			}
		}else{
			$type = 1;
		}
		$this->assign('type',$type);
		$this->assign('model',$model);
		return view();
	}
	public function code($id=0){
		if($id){
			$rule = db('gather')->field('title,rule')->find($id);	
		}
		$json = _format_json($rule['rule']);
		$this->assign('rule',$json);
		$this->assign('title',$rule['title']);
		return view();
	}
	/**
	 * [add_handler 修改/添加控制器]
	 * @param integer $id [description]
	 */
	public function add_handler($id=0){
		$param = request()->param();
		$data = $this->_get_content_rule();
		unset($param['rule']);
		$param['rule']=json_encode($data);
		if($id){
			$param['dates']=time();
			if(!db('gather')->update($param)){
				return ['status'=>0,'msg'=>'修改失败请重试'];
			}
			return ['status'=>1,'msg'=>'修改成功','redirect'=>Url('index')];
		}else{
			$param['date']=time();
			if(!db('gather')->insert($param)){
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
		$_result = $this->_status($id,'gather',$type,'');
		return $_result;
	}
	/**
	 * @author 魏巍
	 * @description 获取抓取规则
	 */
	protected function _get_content_rule(){
		$param = request()->param();
		$content = $param['rule']['content'];
		$list = $param['rule']['list'];
		$_list=[];
		$_content=[];
		
		foreach($content['key'] as $k => $v){
			if(!empty($v)){
				$_temp = explode('|',$content['value'][$k]);
				$_content[$v]=[$_temp[0],$_temp[1]];
			}
		}
		foreach($list['key'] as $k1=>$v1){
			if(!empty($v1)){
				$_temp = explode('|',$list['value'][$k1]);
				$_list[$v1]=[$_temp[0],$_temp[1]];
			}
		}
		
		$_result =[
			'list'=>!empty($_list)?$_list:'',
			'content'=>!empty($_content)?$_content:''
		];
		
		return $_result;
	}
	
}
