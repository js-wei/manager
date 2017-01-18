<?php
// +----------------------------------------------------------------------
// | ThinkPHP [ WE CAN DO IT JUST THINK ]
// +----------------------------------------------------------------------
// | Copyright (c) 2006~2016 http://thinkphp.cn All rights reserved.
// +----------------------------------------------------------------------
// | Licensed ( http://www.apache.org/licenses/LICENSE-2.0 )
// +----------------------------------------------------------------------
// | Author: liu21st <liu21st@gmail.com>
// +----------------------------------------------------------------------

return [
    // 默认模块名
    'default_module'        => 'index',
    // 默认控制器名
    'default_controller'    => 'Index',
    // 默认操作名
    'default_action'        => 'index',
    'captcha'  => [
        // 验证码字符集合
        'codeSet'  => '2345678abcdefhijkmnpqrstuvwxyzABCDEFGHJKLMNPQRTUVWXY', 
        // 验证码字体大小(px)
        'fontSize' => 25, 
        // 是否画混淆曲线
        'useCurve' => false, 
         // 验证码图片高度
        'imageH'   => 50,
        // 验证码图片宽度
        'imageW'   => 220, 
        // 验证码位数
        'length'   => 5, 
        // 验证成功后是否重置        
        'reset'    => true
    ],
    // 视图输出字符串内容替换
    'view_replace_str'       => [
        '__PUBLIC__'=>'/static',
        '__ROOT__'=>'/',
        '__PLUG__'=>'/static/plug',
        '__JS__'=>'/static/index/js',
        '__CSS__'=>'/static/index/css'
    ],
    'template'      => [
        // 模板引擎
        'type'   => 'think',
        //标签库标签开始标签 
        'taglib_begin'  =>  '<',
        //标签库标签结束标记
        'taglib_end'    =>  '>',     
    ],
	'THINK_EMAIL'=>[       //邮件发送
		'SMTP_HOST'=>'smtp.163.com',
		'SMTP_PORT'=>25,
		'SMTP_USER'=>'jswei30@163.com',
		'SMTP_PASS'=>'jswei30',
		'FROM_EMAIL'=>'jswei30@163.com',
		'FROM_NAME'=>'官方邮件'
	],
    'UPLOADE_IMAGE'=>[
        'size'=>1024*1024*5,        //5M最大
        'ext'=>'jpg,png,gif'
    ],
    'UPLOADE_FILE'=>[
        'size'=>1024*1024*8,        //8M最大
        'ext'=>'txt,zip,tar,xls,pdf,doc,docx'
    ],
    'UPLOADE_KINDEDITOR'=>[
        'size'=>1024*1024*8,        //8M最大
        'ext'=>'jpg,png,gif,txt,zip,tar,xls,pdf,doc,docx'
    ],
    'ENCRYPT_KEY'=>'THINK',         //加密key
    'LOG_PATH'=>'/log',
];