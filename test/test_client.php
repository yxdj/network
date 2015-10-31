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
    //'url' => 'http://localhost/index.php',
    'url' => 'http://file.yuan37.test:37/test_server.php',
    //'url' => 'http://file.yuan37.com:37/index.php',
    'row' => ['Xuyuan' => 'test'],
    'get' => ['get1'=>'param2', 'get2'=>['a'=>'param2a','b'=>'param2b']],
    'post' => ['post1'=>'param2', 'post2'=>['a'=>'param2a','b'=>'param2b']],
    'cookie' => ['cookie1'=>'param2', 'cookie2'=>['a'=>'param2a','b'=>'param2b']],
    'file' => [
        //'file1' => ['name'=>'xxx.txt','value'=>file_get_contents('C:\Users\Administrator\Desktop\index.php')],
        'file1' => ['name'=>'php5.chm','value'=>file_get_contents('C:\Users\Administrator\Desktop\php5.chm')],
        //'file2[a]' => ['name'=>'aaa.xxx','value'=>'xxxxxx'],
        //'file2[b]' => ['name'=>'bbb.yyy','value'=>'yyyyyy'],
    ],
])->getContent();

