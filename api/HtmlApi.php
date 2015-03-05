<?php
/**
 * @link https://github.com/yxdj
 * @copyright Copyright (c) 2014 xuyuan All rights reserved.
 * @author xuyuan <1184411413@qq.com>
 */

namespace yxdj\network\api;

use yxdj\network\Api;

class HtmlApi extends Api
{

	//从给定内容中取得所有链接
	static public function a($document='', $real = false) {
		$match=array();
		preg_match_all("'<\s*a\s.*?href\s*=\s*([\"\'])?(?(1) (.*?)\\1 | ([^\s\>]+))'isx", $document, $links); 
		// catenate the non-empty matches from the conditional subpattern
		while (list($key, $val) = each($links[2])) {
			if (!empty($val))
				$match[] = $val;
		} while (list($key, $val) = each($links[3])) {
			if (!empty($val))
				$match[] = $val;
		} 
		$match=array_unique($match);//去除相同
        
        if ($real) {
            $match = self::realPath($match,$real);
        }        
        
		// return the links
		return $match;
	}


	//从给定内容中取得所有链接
	static public function img($document='', $real = false) {
		$match=array();
		preg_match_all("'<\s*img\s.*?src\s*=\s*([\"\'])?(?(1) (.*?)\\1 | ([^\s\>]+))'isx", $document, $links); 
		// catenate the non-empty matches from the conditional subpattern
		while (list($key, $val) = each($links[2])) {
			if (!empty($val))
				$match[] = $val;
		} 
		while (list($key, $val) = each($links[3])) {
			if (!empty($val))
				$match[] = $val;
		} 
		$match=array_unique($match);//去除相同
        
        
        if ($real) {
            $match = self::realPath($match,$real);
        }
        
		// return the links
		return $match;
	}


	//拼接路径完整性
	static public function realPath($str1,$str2){
		//if(!preg_match('/^[a-z]{1,10}:\/\//i',$str2)){$str2='http://'.$str2;}
		$urls= parse_url($str2); 
		!isset($urls['scheme']) && $urls['scheme'] = 'http'; 	//获取协议
		!isset($urls['host']) && $urls['host'] = '';   		 	//获取主机
		!isset($urls['path']) && $urls['path'] = '/';   		//获取路径
		!isset($urls['query']) && $urls['query'] = '';   		//获取参数
		!isset($urls['port']) && $urls['port'] = '80'; 			//获取端口		
		$urls['paths'] = $urls['path'].($urls['query'] ? '?'.$urls['query'] : ''); //组拼完整路经  			
		$scheme = $urls['scheme'].'://'; 
		$host=$urls['host'];
		$port = $urls['port']=='80'?'':':'.$urls['port'];		
		$path=$scheme.$host.$port;//绝对路径
		$path2=$path.$urls['path'];
		$path2=substr($path2,-1)=='/'?$path2:dirname($path2);//以'/'结束直接以此为相对路径，否则上一级
		$path2=substr($path2,-1)=='/'?$path2:$path2.'/';//最后以'/'结束
		
		$url=self::runPath($str1,$path,$path2);	
		return $url;
	}
	
	//拼接路径完整性2,递规
	static public function runPath($str1,$path,$path2){
		if(is_array($str1)){
			$urls=array();
			foreach($str1 as $key => $value){
				$urls[$key]=self::runPath($value,$path,$path2);
			}
			return $urls;
		}

		if(is_string($str1)){
			if(preg_match('/^[a-z]{1,10}:\/\//i',dirname($str1))){
				return $str1;
			}
			
			if(substr($str1,0,1)=='/'){
				return $path.$str1;
			}
			
			if(substr($str1,0,1)!='.'){
				return $path2.$str1;
			}
			
			if(substr($str1,0,2)=='./'){
				return $path2.substr($str1,2);
			}  
			
			if(substr($str1,0,3)=='../'){
				while(substr($str1,0,3)=='../'){
					$str1=substr($str1,3);
					$path2=dirname($path2);			
				}
				return $path2.'/'.$str1;
			}
			return $str1;
		}
	}


	//获取分页
	static public function nextPage($href,$rule,$index){
		if($index==1&&$rule['first']){
			return $href;	
		}else{
			$page=str_replace('@',$index,$rule['page']);
			$url=$href.$page;
			return $url;
		}
	}

	
	//获取匹配内容
	static public function match($str,$rule){
		if(!preg_match_all($rule['reg'],$str,$datas,PREG_SET_ORDER)){
			return false;
		}
		foreach($datas as $key => $value){
			foreach($rule['match'] as $key2 => $value2){
				$info[$key2]=$value[$value2];
			}
			$data[]=$info;
		}
		
		return $data;
	}


	//过滤文档长度标识
	static public function unchunk($result) {
		return preg_replace_callback(
			'/(?:(?:\r\n|\n)|^)([0-9A-F]+)(?:\r\n|\n){1,2}(.*?)'.
			'((?:\r\n|\n)(?:[0-9A-F]+(?:\r\n|\n))|$)/si',
			create_function(
				'$matches',
				'return hexdec($matches[1]) == strlen($matches[2]) ? $matches[2] : $matches[0];'
			),
			$result
		);
	}	
}