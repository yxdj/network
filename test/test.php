<?php
/**
 * @link https://github.com/yxdj
 * @copyright Copyright (c) 2014 xuyuan All rights reserved.
 * @author xuyuan <1184411413@qq.com>
 */

 
//composer autoload
require(dirname(dirname(dirname(__DIR__))).'/autoload.php'); 


use yxdj\network\Api;
use yxdj\network\Cli;


$args = Cli::parseArgs($argv, $argc);


echo Api::getStream()->request([
    'method' => 'POST',
    'url' => 'http://192.168.1.59:37/test_s.php',
    'row' => ['Xuyuan' => 'test'],
    'get' => ['get1'=>'param2', 'get2'=>['a'=>'param2a','b'=>'param2b']],
    'post' => ['post1'=>'param2', 'post2'=>['a'=>'param2a','b'=>'param2b']],
    'cookie' => ['cookie1'=>'param2', 'cookie2'=>['a'=>'param2a','b'=>'param2b']],
])->getContent();

