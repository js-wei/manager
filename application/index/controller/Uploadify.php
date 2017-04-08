<?php
namespace app\index\controller;
use org\Upload;
use think\File;

class Uploadify{
	/**
	 * [uploadimg 上传单个图片]
	 * @return [type] [description]
	 */
	public function upload($file='editormd-image-file'){
	    $file = request()->file($file);
	    $path = DS .'uploads'. DS .'uploadify'. DS . 'image';
	    $info = $file->validate(config('UPLOADE_IMAGE'))->move(ROOT_PATH . 'public'.$path);
	    if($info){
	        $fullPath =  $path.DS.$info->getSaveName();
	        return json(['success' =>1, 'message' => "上传成功",'url' => $fullPath]);
	    }else{
	        return json(['success' =>0, 'message' => "上传失败:".$file->getError()]);
	    }
	}

	/**
	 * [uploads 上传多个文件]
	 * @param  string $file [接收字段]
	 * @return [type]       [description]
	 */
	public function uploads($file='image'){
	    // 获取表单上传文件
	    $files = request()->file($file);
	    $_result=[];
		$i=0;
	    $path = ROOT_PATH . 'public' . DS . 'uploads'. DS . 'image';
	    foreach($files as $file){
	        $info = $file->validate(config('UPLOADE_FILE'))->move($path);
	        if($info){ 
	            $_result[$i]['ext']=$info->getExtension();
	            $_result[$i]['file_name']=$info->getFilename();
	            $_result[$i]['full_path']=$path. DS .$info->getFilename();
				$i++;
	        }else{
	            return $file->getError();
	        }    
	    }
	}
	
    /**
     * 删文章除图片
     * @param $model
     * @param $where
     * @param $field
     * @return bool
     */
    public function delArticleImage($model,$where,$field){
        $flag =true;
        $a = $model->where($where)->find();
		
        if(empty($a) || empty($a[$field])){
            return $flag;
        }
		$content = htmlspecialchars_decode($a[$field]);
		
        $images = get_images($content);
		
        foreach ($images[1]  as $k=> $v){
        	if(strpos($v,'Uploads') !== false){
        		$v =".".$v;
				$ii = explode('/', $src);
				$ii[count($ii)-1]="m_".$ii[count($ii)-1];
				$ii1 = implode('/', $ii);
				if(file_exists($v)){
					if(!unlink("$v")){
						$flag = false;
						break;
					}
				}
				if(file_exists($ii1)){
					if(!unlink("$ii1")){
						$flag = false;
						break;
					}
				}
        	}
        }
        return $flag;
    }
	/**
	 * [delmg 删除图片]
	 * @param  integer $src [删除地址]
	 * @return [type]       [description]
	 */
	public function delmg($src=0){
		if(empty($src)){
			return ['status'=>0,'msg'=>'图片地址不能为空'];
		}
		if(strpos($src,'.')!==true){
			$src = ".".$src;
		}

		if(!unlink($src)){
			return ['status'=>0,'msg'=>'删除失败请重试'];
		}
		return ['status'=>1,'msg'=>'删除成功！'];
	}
}