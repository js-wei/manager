<?php
namespace app\index\controller;
use QL\QueryList;
use GuzzleHttp;

class Index extends Base{
    public function index(){
    	p(config('UPLOADE.path'));
	}
	/**
	 * 抓取数据
	 * @author 魏巍
	 */
	public function pull(){
		$starttime = explode(' ',microtime());
		$list = db('gather')->where(['id'=>1])->select();
		
		foreach($list as $k=>$v){
			$links=explode(',', $v['url']);
			$rule=json_decode($v['rule'],true);
			$this->threading($links, $rule);
			$data = [];
			p($_SESSION['gather']);die;
			foreach($_SESSION['think'] as $k=>$v){
				if(strstr($k,"gather")){
					$article = db('article')->where(['title'=>$v['title']])->count('*');
					$column = db('column')->field('id,title,keywords,description')->where($v['map'])->find();
					unset($v['map']);
					if(!$article && !empty($column)){
						$v['column_id']=$column['id'];
						$v['keywords']=$v['title']."-".$column['keywords'];
						$v['description']=$v['title']."-".$column['description'];
						$data[]=$v;
					}
					session($k,null);
				}
			}
			
			$id = db('article')->insertAll($data);
			//程序运行时间
			$endtime = explode(' ',microtime());
			$thistime = $endtime[0]+$endtime[1]-($starttime[0]+$starttime[1]);
			$thistime = round($thistime,2);
			echo "本网页执行耗时：".$thistime." 秒。<br/>";
		}

//		$list = db('gather')->field('dates',true)->where(['id'=>['lt',2],'status'=>0])->select();
//		foreach($list as $k => $v){
//			$arr = json_decode($v['rule'],true);
//			$result = $this->get_news_list($v['url'],$arr['list']);
//			foreach($result as $k1 => $v1){
//				$article = db('article')->where(['title'=>$v1['title']])->count();
//				if(!$article){
//					if(!array_key_exists('tit',$v1)){
//						$v1['tit']=$v['tit'];
//						$v1['time']='';
//					}
//					if(count($v1)>2 && !empty($v1['time'])){
//						$v1['time'] =date('Y')."-".$v1['time'];
//					}
//					ksort($v1);
//					$_result[$k1] = $this->_get_content($v1['tit'],$v1['href'],$arr['content'],$v1['time']);
//				}
//			}
//		}
//		$_result = array_filter($_result);
//		if(!empty($_result)){
//			 db('article')->insertAll($_result);
//		}
	}

	/**
	 * 抓取内容
	 */
	public function _get_content($type,$href,$rule,$time){
		$str = preg_replace('/[\[\]\{\}]/','',$type);	//去掉[]{}
		$column = db('column');
		$article = db('article');
		$article='';
		$column_id = $column->field('id,keywords,description')->where(['title'=>$str])->find();
		
		if($str!='图片'){
			$article = $this->get_article($href,$rule,$time);
			
			if(count($article)){
				$_result = [];
				$article = $article[0];
				$article['content']=htmlspecialchars($article['content']);
				$article['column_id']=$column_id['id'];
				$article['keywords']=$article['title']."_".$column_id['keywords'];
				$article['description']=$column_id['description']."_".$column_id['description'];
				#$article['path']=$href;
				#$article['tit']=$type;
				
				$article['date']=strtotime1($article['date']);
			}
		}
		return $article;
	}

	/**
	 * 获取图片
	 */
	public function _get_image($path=''){
		//实例化一个Http客户端
		$client = new GuzzleHttp\Client();
		
		//发送一个Http请求
		$response = $client->request('GET', $path,[
			'headers' => [
			 	'User-Agent' => 'Mozilla/5.0 (Windows NT 10.0; WOW64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/55.0.2883.87 Safari/537.36 OPR/42.0.2393.94',
		        'Accept'     => 'application/json',
		        'Content-Encoding' => 'gzip, deflate', 
		        'Content-Type' => 'application/javascript;charset=UTF-8',
		    ]
		]);
		
		$response = mb_convert_encoding($response->getBody(), 'UTF-8', 'UTF-8,GBK,GB2312,BIG5');
		$res = str_replace('/*  |xGv00|d607368ab2e59868b8859771e70f4faf */', '', $response);
		$res = str_replace("'", '"',$res);
		p(json_decode($res,true));die;
	}
	/**
	 * 使用多线程
	 */
	protected function threading($links,$rule){
		$list = $rule['list'];
		$content = $rule['content'];
		try{
			QueryList::run('Multi',[
			    //待采集链接集合
			    'list' =>$links,
			    'curl' => [
			        'opt' =>[
			        	//这里根据自身需求设置curl参数
	                    CURLOPT_SSL_VERIFYPEER => false,
	                    CURLOPT_SSL_VERIFYHOST => false,
	                    CURLOPT_FOLLOWLOCATION => true,
	                    CURLOPT_AUTOREFERER => true,
			        ],
			        //设置线程数
			        'maxThread' => 100,
			        //设置最大尝试数
			        'maxTry' => 5 
			    ],
			    'success' => function($a) use($list,$content,$links){
			    	$response = mb_convert_encoding($a['content'], 'UTF-8', 'UTF-8,GBK,GB2312,BIG5');
			    	if(count($links)==1){
						if(strstr($links[0],'json')){
							$response = json_decode($response,true);
							$a['content'] = $response['data']['article_info'];
						}
			    	}
			        $ql = QueryList::Query($a['content'],$list)->getData(function($item) use($content){
			        	$item1= $this->get_article($item['href'],$content)[0];
						if(count($item1)>0 || $item1!=null){
							$map=[];
				        	if(key_exists('tit', $item)){
								$str = preg_replace('/[\[\]\{\}]/','',$item['tit']);
								$map['title']=$str;
							}else{
								$map['title']=$item1['tit'];
							}
							
							if(!strstr($map['title'],'图')){
								if(key_exists('tit', $item)){
									$str = preg_replace('/[\[\]\{\}]/','',$item['tit']);
									$map['title']=$str;
								}else{
									$map['title']=$item1['tit'];unset($item1['tit']);
								}
								
								if(key_exists('date', $item1)){
									$item1['date'] = strtotime1($item1['date']);
								}else{
									$item1['date'] = strtotime1($item1['date1']);unset($item1['date1']);
								}
								$item1['content']=htmlspecialchars($item1['content']);
								$item1['map']=$map;
							}
						}
						session("gather_".time(),$item1,'gather');
			       });
				   return $ql;
			    },
			    'error'=>function(){
			    	echo "执行出错了";
			    }
			]);
		}catch(Exception $e) {
			echo "执行出错了";
		}
	}
	
	/**
	 * 得到新闻路径
	 */
	protected function get_news_list($url='',$rule='',$block=''){
		$_url=parse_url($url);
		$headers =[
			"User-Agent:Mozilla/5.0 (Windows NT 10.0; WOW64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/55.0.2883.87 Safari/537.36 OPR/42.0.2393.94",
			"Host:{$_url['host']}",
			"Referer:{$_url['scheme']}://{$_url['host']}/"
		];
		
		$return = http($url,'','GET',$headers);
		
		if(is_array($return)){
			$return = $return['data']['article_info'];
	    }
		
		$return=QueryList::Query($return,$rule,$block)->data;
		return $return;
	} 
	
	/**
	 * 采集文档列表
	 */
	protected function get_article_list($url='',$rule='',$block=''){
		$client = new GuzzleHttp\Client();
		$response = $client->request('GET', $url,[
			'headers' => [
			 	'User-Agent' => 'Mozilla/5.0 (Windows NT 10.0; WOW64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/55.0.2883.87 Safari/537.36 OPR/42.0.2393.94',
		        'Accept'     => 'application/default',
		        'Content-Encoding' => 'gzip, deflate', 
		        'Content-Type' => 'application/default;charset=UTF-8',
		    ]
		]);
		$response = mb_convert_encoding($response->getBody(), 'UTF-8', 'UTF-8,GBK,GB2312,BIG5');
		
	}
	
	/**
	 * @author 魏巍
	 * @description 获取详细的文档
	 */
    protected function get_article($url='',$rule='',$time='',$block=''){
    	//实例化一个Http客户端
		$client = new GuzzleHttp\Client();
		
		$response = $client->request('GET', $url,[
			'headers' => [
			 	'User-Agent' => 'Mozilla/5.0 (Windows NT 10.0; WOW64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/55.0.2883.87 Safari/537.36 OPR/42.0.2393.94',
		        'Accept'     => 'application/json',
		        'Content-Encoding' => 'gzip, deflate', 
		        'Content-Type' => 'application/default;charset=UTF-8',
		    ]
		]);
		
		$response = mb_convert_encoding($response->getBody(), 'UTF-8', 'UTF-8,GBK,GB2312,BIG5');
		//$ql = QueryList::Query($response,$rule,$block)->data;
		$ql = QueryList::Query($response,$rule,$block)->getData(function($item){
		    //图片本地化
		    $item['content'] = QueryList::run('DImage',[
		            'content' => $item['content'],
		            'image_path' => '/pull/'.date('Ymd'),
		            'www_root' => config('UPLOADE.path'),
		            'attr' => array('data-original','src','data-gif-url'),
		            'callback' => function($imgObj){
				        $imgObj->removeAttr('data-gif-url');
				    }
		        ]);
		    return $item;
		});
		if(!empty($ql) ){
			if(!empty($time) && count($ql[0])==3){
				$ql[0]['date']=$time;
			}
			return $ql;
		}
	}
}