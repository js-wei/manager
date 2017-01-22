<?php
/**
 * 获取文档的标题
 */
function get_article_title($id=0){
	if(!$id){
		return '';
	}
	$title = db('article')->field('title')->find($id);
	return $title['title'];
}
/**
 * 获取nickname
 */
function get_member_nickname($id=0){
	if(!$id){
		return '';
	}
	$member = db('member')->field('username,email,nickname')->find($id);
	if(!empty($member['nickname'])){
		return $member['nickname'];
	}else if(!empty($member['username'])){
		return $member['username'];
	}else if(!empty($member['email'])){
		return $member['email'];
	}else{
		return '匿名用户';
	}
}

//获取栏目类型
function getPosition($type){
	switch ($type) {
		case 1:
			$t='头部';
			break;
		case 2:
			$t='中部';
			break;
		case 3:
			$t='左侧';
			break;
		case 4:
			$t='右侧';
			break;
		case 5:
			$t='底部';
			break;
	}
	return $t;
}

/**
 * [get_column 获取栏目名]
 * @param  [type] $column_id [栏目ID]
 * @return [type]            [description]
 */
function get_column($column_id=0){
	if(!$column_id){
		return "不限制";
	}else{
		$column = M('column')->field('id,title')->find($column_id);
		return $column['title'];
	}
}