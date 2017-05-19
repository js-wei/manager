<?php
namespace app\admin\controller;

class Volunteer extends Base{
	protected function  _initialize(){
		parent::_initialize();
	}

	public function index(){
		$model = [
			'name'=>'公益管理'
		];
		$list = db('volunteer')->order('id desc')->paginate(10);
		// 查询状态为1的用户数据 并且每页显示10条数据
		$count = db('volunteer')->count('*');
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
        	$vo = db('volunteer')->field('dates',true)->find($id);
        	$this->assign('info',$vo);
		}
		$list = db('article')->field('id,title')->where('status=0')->order('id desc')->select();
		
		$this->assign('list',$list);
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
		$vo = db('volunteer')->field('subscribe,fans,collection')->find($id);
		
		if($t==0){
			$volunteer = db('volunteer')->field('id,username,head,nickname')->where(['id'=>['in',$vo['subscribe']]])->select();
			$this->assign('list',$volunteer);
		}else if($t==1){
			$volunteer = db('volunteer')->field('id,username,head,nickname')->where(['id'=>['in',$vo['fans']]])->select();
			$this->assign('list',$volunteer);
		}else{
			$volunteer = db('article')->field('id,title,description')->where(['id'=>['in',$vo['collection']]])->order('id desc')->select();
			$this->assign('list',$volunteer);
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
			if(!db('volunteer')->update($param)){
				return ['status'=>0,'msg'=>'修改失败请重试'];
			}
			return ['status'=>1,'msg'=>'修改成功','redirect'=>Url('index')];
		}else{
			$param['date']=time();
			if(!db('volunteer')->insert($param)){
				return ['status'=>0,'msg'=>'添加失败请重试'];
			}
			return ['status'=>1,'msg'=>'添加成功','redirect'=>Url('index')];
		}
	}
	
	public function add_account($id=0,$p=''){
		$param = request()->param();
		
		if($param['ssl_cer']!=$param['old_cer']){
			if(is_file($param['old_cer']) && !empty($param['old_cer'])){
				unlink($param['old_cer']);
			}
		}
		if($param['ssl_key']!=$param['old_key']){
			if(is_file($param['old_key']) && !empty($param['old_key'])){
				unlink($param['old_key']);
			}
		}
		unset($param['old_cer']);
		unset($param['old_key']);
		
		if($id){
			$param['dates']=time();
			if(!db('wechat_config')->update($param)){
				return ['status'=>0,'msg'=>'修改失败请重试'];
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