<?php
/**
 * @link https://github.com/yxdj
 * @copyright Copyright (c) 2014 xuyuan All rights reserved.
 * @author xuyuan <1184411413@qq.com>
 */


echo 'GET: ';br();
print_r($_GET);br();br();


echo 'POST: ';br();
print_r($_POST);br();br();

echo 'COOKIE: ';br();
print_r($_COOKIE);br();br();

echo 'FILE: ';br();
print_r($_FILES);br();br();

echo 'HTTP_XUYUAN: ';br();
if (!empty($_SERVER['HTTP_XUYUAN'])) {
    echo $_SERVER['HTTP_XUYUAN'];
}
br();br();
//print_r($_SERVER);





function br(){
    echo "\r\n";
    
}


