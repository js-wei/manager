<?php
namespace app\admin\controller;

class Coupons extends Base{
	protected function  _initialize(){
		parent::_initialize();
	}

	public function index(){
		$model = [
			'name'=>'优惠券管理'
		];
		$list = db('coupons')->paginate(10);
		// 查询状态为1的用户数据 并且每页显示10条数据
		$count = db('coupons')->count('*');
		$this->assign('count',$count);
		$this->assign('model',$model);
		$this->assign('list',$list);
		return $this->fetch();
	}
	/**
	 * [get_columns 获取栏目]
	 * @param  integer $type [栏目类型]
	 * @return [type]        [description]
	 */
	public function get_columns($type=0){
		if($type){
			$where['type']=$type;
		}
		$where['status']=0;
		$where['fid']=['neq',0];
		$vo = db('column')->field('id,title')->where($where)->select();
		//$vo = \service\Category::unlimitForLevel($vo);
		return json($vo);
	}
	/**
	 * [get_goods 获取物品]
	 * @param  integer $id [栏目id]
	 * @return [type]      [description]
	 */
	public function get_goods($id=0){
		if(empty($id)){
			return json(['status'=>0,'msg'=>'错误的请求']);
		}
		$where['column_id']=$id;
		$where['status']=0;
		$vo = db('article')->field('id,title')->where($where)->select();
		return json($vo);
	}
	/**
	 * [add 添加视图]
	 * @param integer $id [description]
	 */
	public function add($id=0){
		$model = [
			'name'=>'优惠券管理'
		];
		if($id){
        	$vo = db('coupons')->field('dates',true)->find($id);
        	$this->assign('info',$vo);
		}
		$this->assign('model',$model);
		return view();
	}
	/**
	 * [couponsd_handler 修改/添加控制器]
	 * @param integer $id [description]
	 */
	public function add_handler($id=0){
		$param = request()->param();
		
		if($id){
			$param['dates']=time();
			if(!db('coupons')->update($param)){
				return ['status'=>0,'msg'=>'修改失败请重试'];
			}
			return ['status'=>1,'msg'=>'修改成功','redirect'=>Url('index')];
		}else{
			$key = $this->_make_coupons($param);
			if(!db('coupons')->insertAll($key)){
	            return ['status'=>0,'msg'=>'优惠券添加失败,请重试'];
	        }
	        return ['status'=>1,'msg'=>'优惠券添加成功','redirect'=>Url('index')];
		}
	}

	protected function _make_coupons($data,$type=0){
		switch ($type) {
			case 0:
				$map['coupons_no']= array('like',$data['_suffix'].'%');
	            //检测前缀是否存在
	            $count = db('coupons')->where($map)->count('*');
	            if($count>0){
	                return ['status'=>0,'msg'=>'卡券添加失败,卡券前缀已存在'];
	            }
	            //构造优惠券
	            for($i=0;$i<$data['coupons_sums'];$i++){
	                if(iconv_strlen($i,'utf-8') == '1'){
	                    $num[$i] = '000'.$i;
	                }elseif(iconv_strlen($i,'utf-8') == '2'){
	                    $num[$i] = '00'.$i;
	                }elseif(iconv_strlen($i,'utf-8') == '3'){
	                    $num[$i] = '0'.$i;
	                }else{
	                    $num[$i] = $i;
	                }
	                $key[$i]['coupons_name'] =$data['coupons_title'];
	                $key[$i]['coupons_val'] =$data['coupons_val'];
	                $key[$i]['coupons_title'] =$data['coupons_title'];
	                $key[$i]['coupons_type'] =$data['coupons_type'];
	                $key[$i]['coupon_cid'] =$data['coupon_cid'];
	                $key[$i]['coupon_content']=htmlspecialchars($data['coupon_content']);
	                $key[$i]['coupons_no'] = $data['_suffix'].getEncyptStr($num[$i]);
	                $key[$i]['coupons_date'] = time();
	            }
	           	return $key;
				break;
			case 1:
				$cols = db('column')->find($data['coupons_list_val']);
	            $coupons = db('coupons')->where(array('coupons_type'=>0))->order('id desc')->find();
	            if(!empty($coupons['coupons_no'])){
	                $ii=str_replace('NO.','',$coupons['coupons_no']);
	                $ii = preg_replace('/^0+/','',$ii);
	                $ii=$ii+1;
	            }else{
	                $ii= 1;
	            }
	            //生成礼券
	            for ($i=0;$i<$data['coupons_sum'];$i++){
	                $key[$i]['coupons_name'] =$cols['name'];
	                $key[$i]['coupons_val'] =$data['coupons_val']."|".$cols['name']."|".$cols['title'];
	                $key[$i]['coupon_cid'] =$data['coupons_list_val'];
	                $key[$i]['coupons_title'] =$data['coupons_title'];
	                $key[$i]['coupons_type'] =$data['coupons_type'];
	                $key[$i]['coupon_content']=htmlspecialchars($data['coupon_content']);
	                $key[$i]['coupons_no'] = "NO.".zeroize($i+$ii,10);
	                $key[$i]['coupons_date'] = time();
	            }
	           	return $key;
				break;
			default:
				return ['status'=>0,'msg'=>'错误的请求'];
				break;
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
		$_result = $this->_status($id,'coupons',$type,'');
		return $_result;
	}
}