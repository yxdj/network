<?php
/**
 * @link https://github.com/yxdj
 * @copyright Copyright (c) 2014 xuyuan All rights reserved.
 * @author xuyuan <1184411413@qq.com>
 */

namespace yxdj\network;

// +----------------------------------------------------------------------
// | yxdj:php-cli
// +----------------------------------------------------------------------
// | Copyright (c) 2014 xuyuan All rights reserved.
// +----------------------------------------------------------------------
// | Author: xuyuan
// +----------------------------------------------------------------------

/*
api并不一定是指向一台服务的
有可能就是本地，有可能多台服务，每台服务提供的接口不一样
这里的api-client就是要做一个统一的调用入口
这里我要这样测试：
1.本地接口:localhost
2.远程api接口：api.yuan37.test,
远程接口也可是指向多台服务,api.yuan37.test主要放自已的机密数据
client提供一套机制去访问各种服务

Api::yuan37('[user:]login',array('name'=>'xuyuan'));

规则说明：server,class,method
server:yuan37
class:yuan37/user
method:login
data:array();

*/

abstract class Api{
	public static $classMap=array();
	public static $obj=array();//实例化后的API对象
	
	//其它方法都定义为私有方法，保证访问的所有的方法都被__callstatic捕获
	/*
    类名：$server,
    方法：$params[0]
    */
    public static function __callstatic($server,$params){
		
		//解析参数
		list($server,$class,$method,$data)=self::parseSpace($server,$params);

        $className=$class;

		//echo $file;exit;
		if(!isset(self::$obj[$className])){
				self::$obj[$className]=new $className($method);	
		}
		$obj=self::$obj[$className];
		
		
		//初始化，执行，返回
		return $obj->_init(array(
			'server'=>'yuan37',
			'class'=>'Yuan37',
			'method'=>$method,
			'data'=>$data
			)
		);
	}
	
	
	
	
	
	
	
	//解析参数，
	private static function parseSpace($server,$params){
        
        $class=$params[0];
        $method=$server;
        
        /*
		//$path=ucfirst($server);						//空间名
		$type=explode(':',$params[0]);				//对象名:方法名
		if(count($type)==1){
			$class=$server;
			$method=$type[0];		
		}else if(count($type)==2){
			$class=$type[0];
			$method=$type[1];		
		}else{
			$class='self';
			$method='error';
		}
		*/
		$data=isset($params[1])?$params[1]:array(); //参数	
		
		return array($server,$class,$method,$data);
	}
	
	
	
	//参数解析出错
	private static function error(){
		return 'ng';
	}

    
    
	protected $server;
	protected $class;
	protected $method;
	protected $data=array();
	protected $result='';
	
	//abstract function run($method,$before,$after);
	//访问一个不存在的方法,先给个机会访问接口自已实现
	public function __call($method,$params){
		if(method_exists($this,'bootstrap')){
			return $this->bootstrap($method);
		}else{	
			return "can't ask a not exists method '{$method}'";
		}
	}
	
	//访问入口
	public function _init($params){
		$this->result='';
		foreach($params as $key => $value){
			$this->$key=$value;
		}
		$method=$this->method;
		return $this->$method();
	}
	
	//映射到服务端，供与此客户端相同规则的服务端使用
	protected function _mapping(){
		$this->data['api']=$this->class.':'.$this->method;
		//执行访问
		$http=new Http();
		if($http->postUrl($this->url,$this->data)=='200'){
			return $http->content;
		}else{
			return $http->getDebug();
		}
	}	
	
	
	//访问,入口,用些相同动作在此处理,并在此接收参数
	protected function _ask(){
		$http=H();
		if($http->postUrl($this->url,$this->data)=='200'){
			return $http->content;
		}else{
			return $http->getDebug();
		}	
	}
        
    public static function className()
    {
        return get_called_class();
    }       
}
