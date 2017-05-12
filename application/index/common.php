<?php

function setpng($o_pic=''){
	//要处理的色阶起始值
	$begin_r = 215;
	$begin_g = 215;
	$begin_b = 215;

	list($src_w,$src_h,$src_type) = getimagesize($o_pic);// 获取原图像信息
	$file_ext = get_ext($o_pic);//获取扩展名
	$target_im = imagecreatetruecolor($src_w,$src_h);//新图

	if($file_ext == 'jpg'){ //转换JPG 开始
	    $src_im = ImageCreateFromJPEG($o_pic);
	    imagecopymerge($target_im,$src_im,0,0,0,0,$src_w,$src_h,100);

	    for($x = 0; $x < $src_w; $x++){
	        for($y = 0; $y < $src_h; $y++){
	            $rgb = imagecolorat($src_im, $x, $y);
	            $r = ($rgb >> 16) & 0xFF;
	            $g = ($rgb >> 8) & 0xFF;
	            $b = $rgb & 0xFF;

	            if($r>$begin_r && $g>$begin_g && $b>$begin_b){   
	                imagecolortransparent($target_im, imagecolorallocate($target_im,$r, $g, $b));                
	            }
	        }
	    }
	}
}
function get_top_left($w=100,$h=200){
	$pos=[];
	$pos['l']=rand(0,$w);
	$pos['t']=rand(0,$h);
	$pos['z']=0;
	$grocery = db('grocery')->where("`top` = ".$pos['t']." or `left`= ".$pos['l'])->find();

	if($grocery['top']==$pos['t']){
		$pos['t']++;
	}
	if($grocery['left']==$pos['l']){
		$pos['l']++;
	}
	if($grocery['top']==$pos['t'] && $grocery['left']==$pos['l']){
		$pos['t']++;
		$pos['l']++;
	}
	if($grocery['top']==$pos['t'] && $grocery['left']==$pos['l'] && !empty($grocery['zindex'])){
		$pos['z']=$grocery['zindex']+1;
	}
	p($pos);
}