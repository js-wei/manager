<?php
namespace app\index\controller;

class Carousel extends Base{
   /**
    * 获取栏目数据列表
    * @param int $id 栏目id
    * @param int $p  当前页数
    */
   public function listing($id=0){
   		$map['status']=0;
		
   		$list = db('article')->field('id,title,url,description,thumb,image')->where($map)->select();
		
		p($list);die;
		
   		return  $list;
   }
   /**
    * [details 获取详细信息]
    * @param  integer $id [文档id]
    * @return [type]      [description]
    */
   public function details($id=0){
   		if(!$id){
			return ['status'=>0,'msg'=>'小子想的倒美,欲取先予,给点好处先:)'];
   		}
   		$article = db('article')->find($id);
   		if(empty($article)){
   			return ['status'=>0,'msg'=>'真是奇怪,你给了些莫名其妙的东西,我努力的绞尽脑汁,也没有想明白是什么东西(:'];
   		}
   		return $article;
   }
}
