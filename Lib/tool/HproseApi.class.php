<?php
class HproseApi{
	public $action='';
	public $data=array();
	public $result='';
	
	
	//统一处理：Api::server(module)->ask(data);
	
	
	//要操作的模块,在此接收要处理的模块
	public function __construct($action){
		$this->action=$action;
	}
	
	
	//访问,入口,用些相同动作在此处理,并在此接收参数
	public function ask($data){
		$http=H();
		$url='http://api.yuan37.test';
		if($http->postUrl($url,$data)=='200'){
			return $http->content;
		}	
	}
	
	
	
	//登录
	public function login($data){
		$data['api']='login';	
		$url='http://api.yuan37.test/hprose.php';
		$client = new HproseHttpClient($url);		
		
		return $client->login('xuyuan');
		
		//echo $this->ask($data);
	}
	
	
	//退出
	public function logout($data){
		$data['api']='logout';
		$url='http://api.yuan37.test/hprose.php';
		$client = new HproseHttpClient($url);		
		
		return $client->logout('xuyuan');
	}	
	

	
}
