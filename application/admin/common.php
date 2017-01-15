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
		return '';
	}
}
