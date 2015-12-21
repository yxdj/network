<?php
/**
 * @link https://github.com/yxdj
 * @copyright Copyright (c) 2014 xuyuan All rights reserved.
 * @author xuyuan <1184411413@qq.com>
 */


echo 'GET: ';
print_r($_GET);
echo '------------';
br();

echo 'POST: ';br();
print_r($_POST);
echo '------------';
br();

echo 'COOKIE: ';br();
print_r($_COOKIE);
echo '------------';
br();

echo 'FILES: ';br();
print_r($_FILES);
echo '------------';
br();




function br(){
    echo "\r\n";
    
}


