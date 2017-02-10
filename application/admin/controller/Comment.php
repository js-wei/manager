<?php
namespace app\admin\controller;
use app\admin\model\Comments;

class Comment extends Base{
	protected function  _initialize(){
		parent::_initialize();
	}

	public function index(){
		$model = [
			'name'=>'评论管理'
		];
		$list = db('comments')->where(['cid'=>0])->paginate(10);
		// 查询状态为1的用户数据 并且每页显示10条数据
		$count = db('comments')->count('*');
		$this->assign('count',$count);
		$this->assign('model',$model);
		$this->assign('list',$list);
		return $this->fetch();
	}

	public function add($id=0){
		$model = [
			'name'=>'评论管理'
		];
		if($id){
        	$vo = db('comments')->field('dates',true)->find($id);
			$count = db('comments')->where(['uid'=>$vo['uid']])->count('*');
        	$this->assign('info',$vo);
			$this->assign('count',$count);
		}
		$this->assign('model',$model);
		return view();
	}
	
	public function all($id=0,$uid=0){
		if(!$uid||!$id){
			//return ['status'=>0,'msg'=>'修改失败请重试'];
		}
		$vo = db('comments')->field('dates',true)->find($id);
		$list = db('comments')->field('dates',true)->where(['aid'=>$vo['aid']])->select();
		
		if(empty($list)){
			return ['status'=>0,'msg'=>'没有更多的数据'];
		}
		foreach($list as $k=>$v){
			$m = Comments::get($v['id']);
			$list[$k]['nickname'] = get_member_nickname($v['uid']);
			$list[$k]['head'] = $m->infor['head']?$m->infor['head']:'/static/admin/img/avatars/head.jpg';
			$list[$k]['title'] = $m->article['title'];
			$list[$k]['sortID'] = $v['cid'];
			$list[$k]['date'] = date('Y-m-d',$v['date']);
			unset($list['aid']);
			unset($list['uid']);
		}
		$this->assign('list',json_encode($list));
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
			if(!db('comment')->update($param)){
				return ['status'=>0,'msg'=>'修改失败请重试'];
			}
			return ['status'=>1,'msg'=>'修改成功','redirect'=>Url('index')];
		}else{
			$param['date']=time();
			if(!db('comment')->insert($param)){
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
		$_result = $this->_status($id,'comments',$type,'');
		return $_result;
	}
}