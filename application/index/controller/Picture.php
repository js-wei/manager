<?php
namespace app\index\controller;

class Picture extends Base{
    protected function _initialize(){
        parent::_initialize();
    }

    /**
     * 获取轮播图
     * @param int $cid
     * @param int $len
     * @return mixed
     */
    public function get_banner_list($cid=0,$len=3){
        $map='';
        if($cid){
            $map['cid']=$cid;
        }
        $list = db('carousel')->field('id,title,url,image')->where($map)->limit($len)->selcte();
        if(empty($list)){
            return jsonp(['status'=>0,'msg'=>'没有任何的轮播图']);
        }
        return jsonp(['status'=>1,'data'=>$list]);
    }

    /**
     * 获取轮播图详情
     * @param int $id
     * @return mixed
     */
    public function get_banner($id=0){
        if(!$id){
            return jsonp(['status'=>0,'msg'=>'错误的请求']);
        }
        $data = db('carousel')->field('id,title,url,image,description,position')->find($id);
        if(empty($data)){
            return jsonp(['status'=>0,'msg'=>'好像没有此项信息']);
        }
        return jsonp(['status'=>1,'data'=>$data]);
    }
}