<?php
class ToolApi extends SetApi{
	public $url='http://php.net';
	
	public function __construct($action){
		$this->action=$action;
	}
	
	
	public function ask(){
		$url=FlyCli::ask('pleace input url: ');
		
		$http=H();
		
		if($http->getUrl($url)=='200'){
			return $http->getKeyword();
		}else{
			return $http->getDebug();
		}
		
		
	}
	
	
}
