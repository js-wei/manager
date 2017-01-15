<?php
namespace app\admin\controller;
use org\Upload;
use think\File;

class Uploadify extends Base{
	protected function _initialize(){
		parent::_initialize();
	}
	/**
	 * [uploadimg 上传单个图片]
	 * @return [type] [description]
	 */
	public function uploadimg($file='image'){
	    $file = request()->file($file);
	    $path = DS .'uploads'. DS .'uploadify'. DS . 'image';
	    $info = $file->validate(config('UPLOADE_IMAGE'))->move(ROOT_PATH . 'public'.$path);
	    if($info){
	        $fullPath =  $path.DS.$info->getSaveName();
	        return $fullPath;
	    }else{
	        return $file->getError();
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
	    $path = ROOT_PATH . 'public' . DS . 'uploads'. DS . 'file';
	    foreach($files as $file){
	        $info = $file->validate(config('UPLOADE_FILE'))->move($path);
	        if($info){ 
	            $_result['ext']=$info->getExtension();
	            $_result['file_name']=$info->getFilename();
	            $_result['full_path']=$path. DS .$info->getFilename();
	        }else{
	            return $file->getError();
	        }    
	    }
	}
	/**
	 * [KindEditorUpload KindEditor编辑器上传文件]
	 */
	public function KindEditorUpload(){

		$file = request()->file('imgFile');
		//上传路径
		if(input('dir')=='image'){
			//图片保存地址
			$path = ROOT_PATH . 'public' . DS . 'uploads'. DS . 'KindEditor'. DS .'image';
		}else{
			//文件保存地址
			$path = ROOT_PATH . 'public' . DS . 'uploads'. DS . 'KindEditor'. DS .'file';
		}
		
		//字体地址
		$font = ROOT_PATH . 'public' . DS .'static'.DS .'font'. DS .'HYQingKongTiJ.ttf';
		//
		$logo = ROOT_PATH . 'public' . DS .'static'.DS .'logo.png';
		//
		$info = $file->validate(config('UPLOADE_KINDEDITOR'))->move($path);
        if($info){
        	$fullPath =  DS.'uploads'.DS."KindEditor".DS.input('dir').DS.$info->getSaveName();
        	$path = $path.DS.$info->getSaveName();
        	switch (input('water')) {
	    		case '0':	//网址水印
	    			$image = \think\Image::open($path);
					// 给原图左上角添加水印并保存water_image.png
					$image->text($this->site['url'],$font,15)->save($path);
					return json(['error'=>0,'url'=>$fullPath]);
	    			break;
	    		case '1':	//图片水印
	    			if(!is_file($logo)){
						return json(['error'=>1,'message'=>'不存在的LOGO,请在'.ROOT_PATH . 'public' . DS .'static'.',文件夹下放入:logo.png','url'=>'']);
	    			}
	    			$image = \think\Image::open($path);
					// 给原图左上角添加透明度为50的水印并保存alpha_image.png
					$image->water($logo,\think\Image::WATER_NORTHWEST,50)->save($path);
					return json(['error'=>0,'url'=>$fullPath]);
	    			break;
	    		case '2':	//自定义文字
	    			$image = \think\Image::open($path);
					// 给原图左上角添加透明度为50的水印并保存alpha_image.png
					$image->text(input('font'),$font,15)->save($path);
					return json(['error'=>0,'url'=>$fullPath]);
	    			break;
	    		case '-1':	//无水印
	    		default:
	    		 	return json(['error'=>0,'url'=>$fullPath]);
	    			break;
	    	}
        }else{
            return json(['error'=>1,'message'=>$file->getError(),'url'=>'']);
        } 
	}

	/**
     * 删除文件
     * @param $model
     * @param $where
     * @param $file
     */
    public function delmgByWhere($model,$where,$file){
        $m = $model->where($where)->find();
        $src = $m[$file];
        if(empty($src)){
            return array('status'=>0,'msg'=>'图片地址不能为空');
        }
        if(strpos($src,'.')!==true){
            $src = ".".$src;
        }

        $ii = explode('/', $src);
        $ii[count($ii)-1]="m_".$ii[count($ii)-1];
        $ii1 = implode('/', $ii);

        if(file_exists($src)){
            if(!unlink($src) || !unlink($ii1)){
                return array('status'=>0,'msg'=>'图片删除失败，请重试！');
            }
        }
        if(file_exists($ii1)){
            if(!unlink($ii1)){
                return array('status'=>0,'msg'=>'缩略图删除失败，请重试！');
            }
        }
        return true;
    }
	/**
	 * 删除文件
	 * @param $model
	 * @param $where
	 * @param $file
	 */
	public function delmgByWhere1($model,$where,$file){
		$m = $model->where($where)->find();
		$src = $m[$file];
		
		$flag = true ;
		if(empty($src)){
			return $flag;
		}
		
		if(strpos($src,'.')!==true){
			$src = ".".$src;
		}
		
		$ii = explode('/', $src);

		$ii[count($ii)-1]="m_".$ii[count($ii)-1];
		$ii1 = implode('/', $ii);
		
		if(file_exists($src)){
			if(!unlink("$src")){
				$flag = false;
			}
		}
		if(file_exists($ii1)){
			if(!unlink("$ii1")){
				$flag = false;
			}
		}
		return $flag;
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
	 * @author 魏巍
	 * @description 删除图集
	 * @model 模型
	 * @where 查询条件
	 * @field 删除字段
	 */
	public function delImageAtlas($model,$where,$field='atlas'){
		$flag =true;
        $a = $model->where($where)->find();
		
        if(empty($a) || empty($a[$field])){
            return $flag;
        }
		$images = explode(',', $a[$field]);
		foreach ($images  as $k=> $v){
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