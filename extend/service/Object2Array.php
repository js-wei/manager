<?php
namespace Service;
/**
* 
*/
class Object2Array{ //得到数组
    public static function parse($e){ 
        $e=(array)$e; 
		
        foreach($e as $k=>$v){
            if(gettype($v)=='resource'){
            	return;
            } 
            if(gettype($v)=='object' || gettype($v)=='array'){
            	$e[$k]=self:: parse($v); 
            } 
            	
        } 
		
        return $e; 
    }
	
	public static function parse1($e){ 
        $e = json_encode($e);
        return json_decode($e,true); 
    }
}