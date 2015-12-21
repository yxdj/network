<?php
/**
 * @link https://github.com/yxdj
 * @copyright Copyright (c) 2014 xuyuan All rights reserved.
 * @author xuyuan <1184411413@qq.com>
 */


//composer autoload
require('../../../autoload.php'); 

use yxdj\filesystem\Upload;


if (empty($_SERVER['HTTP_XUYUAN']) || $_SERVER['HTTP_XUYUAN'] != 'test') {
    exit('you do not have access!');
}

//-------------------------------------------
//download

if (!empty($_POST['type']) && $_POST['type'] == 'download') {
    if (empty($_POST['download'])) {
        exit('Error: (server)please input download file');
    } else {
        $downloadfile = $_POST['download'];
    }
    
    if (is_file(__DIR__.'/file/'.$downloadfile)) {
        echo file_get_contents(__DIR__.'/file/'.$downloadfile);
    } else {
        echo '######';
    }
    
    
    
    
    exit;
    
}




//------------------------------------------
$upload = new Upload();
$file = $_FILES['file1'];
$root = __DIR__;
$cut = 'file';
$rname = $file['name'];
$result = $upload->upload($file,$root,$cut,$rname);
if (!$result) {
    echo $upload->error;
} else {
    echo 'ok';
}

exit;

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


