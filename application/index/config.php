<?php
return [
	'view_replace_str'  =>  [
		'__ROOT__'=>'/',
		'__PUBLIC__'=>'/static',
		'__PLUG__'=>'/static/plug',
	    '__JS__'=>'/static/admin/js',
	    '__CSS__'=>'/static/admin/css',
	    '__IMAGES__'=>'/static/admin/img',
	    '__SELF__'=>$_SERVER['REQUEST_URI'] 
	],
	'default_return_type'=>'html',
    // 默认AJAX 数据返回格式,可选json xml ...
    'default_ajax_return'    => 'json',
    // 默认JSONP格式返回的处理方法
    'default_jsonp_handler'  => 'jsonpReturn',
    // 默认JSONP处理方法
    'var_jsonp_handler'      => 'callback',
	'paginate'               => [
	    'type'     => 'bootstrap',
	    'var_page' => 'p',
	    'list_rows'=>10
	]
];