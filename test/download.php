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

/*
if (empty($args['s'])) {
    exit('please input `-s`.');
}

$uploadfile = $args['s'];
*/

if ($argc < 2 || empty($argv[1])) {
    exit('Error: please input download file');
}

$downloadfile = $downloadfile_o = $argv[1];



/*
$uploadfile = Cli::ask('please input path for upload file:');
//print_r($args);exit;

$uploadfile = './' .$uploadfile; 
*/


$data =  Api::getStream()->request([
    'method' => 'POST',
    //'url' => 'http://localhost/index.php',
    'url' => 'http://192.168.1.59:37/test_server.php',
    //'url' => 'http://ys.com:90/test_server.php',
    //'url' => 'http://file.yuan37.com:37/index.php',
    'row' => ['Xuyuan' => 'test'],
    'get' => ['get1'=>'param2', 'get2'=>['a'=>'param2a','b'=>'param2b']],
    'post' => ['type'=>'download', 'download'=>$downloadfile],
    'cookie' => ['cookie1'=>'param2', 'cookie2'=>['a'=>'param2a','b'=>'param2b']],
])->getContent();

if ($data == '######') {
    echo 'not fond this file!';exit;
}

if (file_put_contents(basename($downloadfile), $data)) {
    echo 'ok';
} else {
    echo 'ng';
}