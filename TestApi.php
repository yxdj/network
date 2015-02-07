<?php
/**
 * @link https://github.com/yxdj
 * @copyright Copyright (c) 2014 xuyuan All rights reserved.
 * @author xuyuan <1184411413@qq.com>
 */

namespace yxdj\network;


class TestApi extends Api{
	public $url='http://php.net';		
	

    /*
	public function bootstrap(){
		return $this->_mapping();
	}
	*/
	
	public function test(){
		return 'test';
	}

	
	
}