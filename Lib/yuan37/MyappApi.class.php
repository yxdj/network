<?php
class MyappApi extends SetApi{
	public $url='http://api.yuan37.com';	
	
	public function bootstrap(){
		if(in_array($this->method,array('test','test2'))){
			return $this->_mapping();
		}else{
			return 'ng';
		}
	}

	public function xxx(){
		$this->data['ask']=789;
		return $this->_mapping();
	}
	
	
	public function testv(){
		return $this->_mapping();
	}
	
}
