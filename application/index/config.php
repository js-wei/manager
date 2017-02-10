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
	'default_return_type'=>'jsonp',
    'default_jsonp_handler'	 => 'callback',
	'paginate'               => [
	    'type'     => 'bootstrap',
	    'var_page' => 'p',
	    'list_rows'=>10
	]
];