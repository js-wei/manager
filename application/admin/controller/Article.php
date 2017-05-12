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
		
		$_list = db('provinces')->field('id,province')->where('status=0')->select();
		
		$this->assign('column_list',$list);
		$this->assign('province_list',$_list);
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
		$param['attention']=htmlspecialchars(input('attention'));
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
	
	public function tiyan($id=0,$aid=0){
		$model = [
			'name'=>'体验管理'
		];
		$where='';
		if($aid){
			$where['fid']=$id;
		}
		
		if(!$id){
			$this->error('参数错误');
		}
		$list = db('taste')->where($where)->order('date desc')->paginate(15);
		$count = db('taste')->count('*');
		$this->assign('count',$count);
		$this->assign('aid',$aid);
		$this->assign('model',$model);
		$this->assign('list',$list);
		return view();
	}
	
	public function comment($id=0,$aid){
		$model = [
			'name'=>'评论管理'
		];
		if($id){
        	$vo = db('taste')->field('dates',true)->find($id);
			$count = db('comments')->where(['aid'=>$vo['fid']])->count('*');
        	$this->assign('info',$vo);
			$this->assign('count',$count);
		}
		$this->assign('model',$model);
		$this->assign('aid',$aid);
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
			$m = model('comments')->find($v['id']);
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
	public function see($id=0,$aid=0){
		if(!$id){
			$this->error('参数错误');
		}
		$model = [
			'name'=>'体验审核'
		];
		$taste = db('taste')->find($id);
		$this->assign('info',$taste);
		$this->assign('model',$model);
		$this->assign('aid',$aid);
		return view();
	}
	
	public function set_see($id=0,$pid=0,$aid=0,$t=0,$p=1){
		$db = db('taste');
		if($t==0){
			if(!$db->update([
				'id'=>$id,
				'status'=>0,
				'dates'=>time()
			])){
				return ['status'=>0,'msg'=>'操作失败'];
			}
			return ['status'=>1,'msg'=>'操作成功','redirect'=>Url('tiyan?aid='.$aid.'&p='.$p.'&id='.$pid)];
		}else{
			if(!$db->update([
				'id'=>$id,
				'status'=>3,
				'dates'=>time()
			])){
				return ['status'=>0,'msg'=>'操作失败'];
			}
			return ['status'=>1,'msg'=>'操作成功','redirect'=>Url('tiyan?aid='.$aid.'&p='.$p.'&id='.$pid)];
		}
	}
	
	public function add_tiyan($pid=0,$aid=0,$id=0){
		
		$model = [
			'name'=>'添加体验'
		];
		if($id){
        	$vo = db('taste')->field('dates',true)->find($id);
        	$this->assign('info',$vo);
		}
		$this->assign('model',$model);
		$this->assign('aid',$aid);
		return view();
	}
	
	public function add_tiyan_handler($id=0,$aid=0,$pid=0){
		$param = request()->param();
		unset($param['aid']);
		unset($param['pid']);
		unset($param['p']);
		$param['fid'] = $pid;
		
		if($id){
			$param['dates']=time();
			if(!db('taste')->update($param)){
				return ['status'=>0,'msg'=>'修改失败请重试'];
			}
			return ['status'=>1,'msg'=>'修改成功','redirect'=>Url('tiyan?aid='.$aid.'&id='.$pid)];
		}else{
			$param['date']=time();
			if(!db('taste')->insert($param)){
				return ['status'=>0,'msg'=>'添加失败请重试'];
			}
			return ['status'=>1,'msg'=>'添加成功','redirect'=>Url('tiyan?aid='.$aid.'&id='.$pid)];
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
	
	public function set_status($id,$type){
		$type = ($type=="delete-all")?"delete":$type;
		$_result = $this->_status($id,'taste',$type,'',Url('tiyan?id='.input('id').'aid='.input('aid').'&p='.input('p')));
		return $_result;
	}
}