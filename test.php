<?php
/**
 * @link https://github.com/yxdj
 * @copyright Copyright (c) 2014 xuyuan All rights reserved.
 * @author xuyuan <1184411413@qq.com>
 */

use yxdj\network\Http;
use yxdj\network\Api;
use yxdj\network\TestApi;
use yxdj\network\Lib\yuan37\Yuan37;


require('Http.php');
require('Api.php');
require('TestApi.php');
require('Lib/yuan37/Yuan37.php');


echo Api::test(
    TestApi::className(),
    array('username'=>'xuyuan', 'password' => 'xuyuan')
);

echo Api::login(
    Yuan37::className(),
    array('username'=>'xuyuan', 'password' => 'xuyuan')
);


/*
//test http
$http = new Http();
$url = 'http://php.net';
if ($http->getUrl($url) == 200) {
    echo $http->getKeyword();
} else {
    echo $http->getDebug();
}
*/