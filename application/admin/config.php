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
	'PAGE_SIZE'=>15,
	'session'                => [
	    'prefix'         => 'think',
	    'type'           => '',
	    'auto_start'     => true,
	],
];