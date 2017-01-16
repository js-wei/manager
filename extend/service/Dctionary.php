<?php
/**
 * Created by PhpStorm.
 * User: 魏巍
 * Date: 2016/6/22
 * Time: 13:24
 */
class Dictionary {
    private $datastore=array();

    /**
     * 添加数据
     * @param string $key
     * @param $val
     */
    public function add($key='',$val){
        $this->datastore[$key]=$val;
    }

    /**
     * 查找数据
     * @param string $key
     * @return mixed
     */
    public function find($key=''){
        return $this->datastore[$key];
    }

    /**
     * 删除数据
     * @param string $key
     */
    public function remove($key=''){
        unset($this->datastore[$key]);
    }

    /**
     * 显示数据
     * @return array
     */
    public function showAll(){
        foreach ($this->datastore as $k => $v){
            echo "$k->$v<br />";
        }
    }

    /**
     * 获取字典长度
     * @return int
     */
    public function count(){
        return count($this->datastore);
    }

    /**
     * 清除数据
     */
    public function claer(){
        $this->datastore=array();
    }

    /**
     * 排序
     * @return array
     */
    public function sort(){
        ksort($this->datastore);
        return $this->datastore;
    }
}