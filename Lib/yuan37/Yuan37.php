<?php
namespace yxdj\network\Lib\yuan37;

use yxdj\network\Api;

class Yuan37 extends Api{
	public $url='http://api.yuan37.test';	
	

	/*
	
	将client方法映射到server
	
	
	*/

	
	public function loginv(){
		$this->data['password']=789;
		return $this->_mapping();
	}

	
	
	public function bootstrap(){
		return $this->_mapping();
	}
	
    public function test()
    {
        return 'test...';
    }

	public function login1()
    {
        return 'login...';
    }
}
