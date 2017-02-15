<?php
namespace app\index\model;
use think\Model;

class Article extends Model{
    public $pre;
    public $next;
    public $column;
    public $article;
	//自定义初始化
    protected function initialize(){
        //需要调用`Model`的`initialize`方法
        parent::initialize();
        //TODO:自定义的初始化
    }
	public function __construct($id){
        $this->article = $a = $this->get_article($id);
        $this->set_hits($id);
        $this->pre = $this->get_pre($id,$a['column_id']);
        $this->next = $this->get_next($id,$a['column_id']);
        $this->column = $this->get_colunm($a['column_id']);
    }

    /**
     * 获取所有数据
     * @return array
     */
    public function getAllData(){
        return [
            'next'=>$this->next,
            'column'=>$this->column,
            'pre'=>$this->pre,
            'article'=>$this->article,
        ];
    }

    /**
     * @param $id
     * @return mixed
     */
    protected  function get_article($id){
<<<<<<< HEAD
        $article = db('article')->field('id,column_id,title,author,keywords,description,image,content,hits,date')->find($id);
        $article['content']=htmlspecialchars_decode($article['content']);
        $temp = $article['date'];
        $article['date']=date('Y-m-d',$temp);
        $article['date1']=date('Y-m-d H:i:s',$temp);
=======
        $article =  db('Article')->field('date',true)->find($id);
        $article['content'] = htmlspecialchars_decode($article['content']);
        $content = $article['content'];
        $url = session('site.url').'/uploads/pull/';
        $pattern = '/\/pull\//';
        $article['content'] = preg_replace($pattern,$url,$content);
>>>>>>> 205faae7a1d7d1b717586bfeb814d91e8d3b300f
        return $article;
    }

    /**
     * @param $id
     * @param $cid
     * @return mixed
     */
    protected  function get_pre($id,$cid){
        $where['id']=array('gt',$id);
        $where['column_id']=$cid;
        return db('Article')->field('id,title')->where($where)->limit(1)->order('id asc')->find();
    }

    /**
     * @param $id
     * @param $cid
     * @return mixed
     */
    protected  function get_next($id,$cid){
        $where['id']=array('lt',$id);
        $where['column_id']=$cid;
        $next = db('Article')->field('id,title')->where($where)->limit(1)->order('id desc')->find();
        return $next;
    }

    /**
     * @param $cid
     * @return mixed
     */
    protected  function get_colunm($cid){
        return db('column')->field('id,title,name')->find($cid);
    }

    /**
     * @param $id
     */
    protected  function set_hits($id){
        db('Article')->where(array('id'=>$id))->setInc('hits',1);
    }
}
