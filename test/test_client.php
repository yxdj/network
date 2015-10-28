<?php
/**
 * @link https://github.com/yxdj
 * @copyright Copyright (c) 2014 xuyuan All rights reserved.
 * @author xuyuan <1184411413@qq.com>
 */


//composer autoload
require('../../../autoload.php'); 


use yxdj\network\Api;


echo Api::getStream()->request([
    'method' => 'POST',
    'url' => 'http://localhost/index.php',
    'row' => ['Xuyuan' => 'test'],
    'get' => ['get1'=>'param2', 'get2'=>['a'=>'param2a','b'=>'param2b']],
    'post' => ['post1'=>'param2', 'post2'=>['a'=>'param2a','b'=>'param2b']],
    'cookie' => ['cookie1'=>'param2', 'cookie2'=>['a'=>'param2a','b'=>'param2b']],
    'file' => [
        'file1' => ['name'=>'111.txt','value'=>'123456'],
        'file2[a]' => ['name'=>'aaa.xxx','value'=>'xxxxxx'],
        'file2[b]' => ['name'=>'bbb.yyy','value'=>'yyyyyy'],
    ],
])->getContent();

