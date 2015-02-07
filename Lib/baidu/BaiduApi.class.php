<?php
/*
api并不一定是指向一台服务的
有可能就是本地，有可能多台服务，每台服务提供的接口不一样
这里的api-client就是要做一个统一的调用入口
这里我要这样测试：
1.本地接口:localhost
2.远程api接口：api.yuan37.test,
远程接口也可是指向多台服务,api.yuan37.test主要放自已的机密数据
client提供一套机制去访问各种服务
*/
class BaiduApi extends SetApi{
	public $url='http://www.baidu.com';
	
	public function __construct($action){
		$this->action=$action;
	}
	
	
	public function data($data){
		$action=$this->action;
		
		return $this->$action($data);
	}
	
	
	public function all($data){
		//echo $api;
		$http=H();
		if($http->postUrl($this->url,$this->data)=='200'){
			return $http->content;
		}	
	}
	
	
	
	public function charset(){
	
		//echo $api;
		$http=H();
		if($http->getUrl($this->url,$this->data)=='200'){
			return $http->getCharset();
		}else{
			return $http->getDebug();
		}
		
	}
	
	public function keyword(){
	
		//echo $api;
		$http=H();
		if($http->getUrl($this->url,$this->data)=='200'){
			return $http->getKeyword();
		}else{
			return $http->getDebug();
		}
		
	}	
	
}
