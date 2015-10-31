<?php
/**
 * @link https://github.com/yxdj
 * @copyright Copyright (c) 2014 xuyuan All rights reserved.
 * @author xuyuan <1184411413@qq.com>
 */

namespace yxdj\network;

class Cli
{
	//解析参数
	static public function parseArgs($argv,$argc){
		$args=array();
		$null=false;
		foreach($argv as $key=>$value){
			if(substr($value,0,1)=='-'){
				$index=substr($value,1);
				$args[$index]='*';
				$null=true;	
			}elseif(isset($index) && $null){
				$args[$index]=$value;
				$null=false;
			}
		}
		return $args;
	}	

	//停留模式
	static public function stop($info='enter key go on...',$default=false){
		while(true){
			fwrite(STDOUT,$info);
			$answer=trim(fgets(STDIN));
			if(!$default ||$default==$answer)	return;	
		}
	}


	//选择模式
	static public function option($info='please input:',$options=array(),$default=false){
		while(true){
			fwrite(STDOUT,$info);
			$answer=trim(fgets(STDIN));
			if(in_array($answer,$options)){
				return $answer;
			}else if($default){
				return $default;
			}else{
				fwrite(STDOUT,'please select with:'.implode(',',$options)."\r\n");
				continue;
			}		
		}

	}


	//书写模式
	static public function ask($info='please input:',$over=false){	
		if(is_string($over)){//多行模式
			fwrite(STDOUT,$info."\r\n");
			$rows=array();
			
			while(true){
				$row=trim(fgets(STDIN));
				if($row==$over){
					break;
				}else{
					$rows[]=$row;
				}
			}
			return $rows;		
		}else{//单行模式
			fwrite(STDOUT,$info);
			$answer=trim(fgets(STDIN));
			return $answer;
		}
	}
		

}