<?php
namespace app\index\controller;
use think\Controller;
use think\Session;

class Base extends Controller{
	
    protected function _initialize(){
    	header('Content-type:text/html;charset=utf-8;');
		set_time_limit(0);
		//常用变量
		$this->action = request()->action();
		$this->controller = request()->controller();
		$this->module = request()->module();
		$this->assign('action',strtolower($this->action));
		$this->assign('controller',strtolower($this->controller));
		$this->assign('module',strtolower($this->module));
		$this->site = db('Config')->order('id asc')->find();
		session('site',$this->site);
    }

}
