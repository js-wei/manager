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


/**
* Formats a JSON string for pretty printing
*
* @param string $json The JSON to make pretty
* @param bool $html Insert nonbreaking spaces and <br />s for tabs and linebreaks
* @return string The prettified output
*/
function _format_json($json, $html = false) {
	 $tabcount = 0;
	 $result = '';
	 $inquote = false;
	 $ignorenext = false;
	 if ($html) {
	  	$tab = "   ";
	  	$newline = "<br/>";
	 } else {
	  	$tab = "\t";
	  	$newline = "\n";
	 }
	 for($i = 0; $i < strlen($json); $i++) {
		  $char = $json[$i];
		  if($ignorenext) {
		  	   $result .= $char;
		  	   $ignorenext = false;
		  }else {
			  switch($char) {
			   case '{':
				   $tabcount++;
				   $result .= $char . $newline . str_repeat($tab, $tabcount);
				   break;
			   case '}':
				   $tabcount--;
				   $result = trim($result) . $newline . str_repeat($tab, $tabcount) . $char;
				   break;
			   case ',':
				   $result .= $char . $newline . str_repeat($tab, $tabcount);
				   break;
			   case '"':
				   $inquote = !$inquote;
				   $result .= $char;
				   break;
			   case '\\':
				   if ($inquote) $ignorenext = true;
				   $result .= $char;
			   break;
			   default:
			   		$result .= $char;
			  }
		  }
	 }
	 return $result;
}
