<?php
/**
 * @link https://github.com/yxdj
 * @copyright Copyright (c) 2014 xuyuan All rights reserved.
 * @author xuyuan <1184411413@qq.com>
 */







//composer autoload
require('../../autoload.php'); 

use yxdj\network\api\TestApi;

//debug: show request and response，
//echo TestApi::debug(array('url' => 'http://php.net'));

$csrf = '就是这么吊，怎么样';
$result = TestApi::text(array(
    'method' => 'POST',
    'url' => 'http://yii.app.com/test4/456.html',
    'get' => ['get_data'=>['aaa'=>555,'bbb'=>333]],
    'row' => ['X-CSRF-Token' => yiicsrf($csrf), 'XUYUAN'=>'GOOD'],
    'post' => ['test'=>'555','_csrf'=>yiicsrf($csrf)],
    'cookie' => yiicookie(['aaa'=>'bbb', 'ccc'=>'dddd','_csrf'=>$csrf]),
    'file' => [
    'file[aa]'=>['name'=>'file1.txt','value'=>'55555'],
    'file[bb]'=>['name'=>'file2.jpg','value'=>'66666666'],
    'file[cc]'=>['name'=>'file3.jpg','value'=>'66666666'],
    'myfile' => ['name'=> 'ssh.png', 'value'=>file_get_contents('ssh.png')],
    //'myfile' => ['name'=> 'ssh.png', 'value'=>'xxxxxxx'],
    ],
));

//echo $result;
echo file_put_contents('test.html', $result);





//加密cookie
function yiicookie($value=[]){
    foreach($value as $key => &$val){
        $data = serialize($val);
        $validationKey='z7YpelYBsD7ETFsy6gkN1puZgTHrMKhO';
        $hash = hash_hmac('sha256', $data, $validationKey, false);//注意加密参数
        if (!$hash) {
            throw new InvalidConfigException('Failed to generate HMAC with hash algorithm: ');
        }
        $val = $hash.$data;
    }
    return $value;
}

/*
前者，后者，结果
结果为长
结果一定要和短的比才正常,此时短和上次一样变长比较
要是和长的比较，得到的一定是长的，那就肯定比短的长

而结果是和后面的在比，后面的是mask,mask一定要短

token要比mask大，mask默认为8，token为32

function xorTokens($token1, $token2){
    $n1 = mb_strlen($token1, '8bit');
    $n2 = mb_strlen($token2, '8bit');
    if ($n1 > $n2) {
        $token2 = str_pad($token2, $n1, $token2);
    } elseif ($n1 < $n2) {
        $token1 = str_pad($token1, $n2, $n1 === 0 ? ' ' : $token1);
    }

    return $token1 ^ $token2;
} 

1.token不比mask小，token不能为空，mask可以为空
2.mask默认是8，那生成时也必需以8生成，方便切分
3.token默认32
*/
function yiicsrf($token='123456'){
    $length=8;
    $chars = 'ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789_-.';
    //获取一段随机字符：把上述字符复制五次，随机打乱，截取指定长度
    $mask = substr(str_shuffle(str_repeat($chars, 5)), 0, $length);
    // The + sign may be decoded as blank space later, which will fail the validation
    //把token与mask进行xo运算，前面加上mask这个mask字符串
    
    
    $token2 = $token;
    $mask2 = $mask;
    
    $n1 = mb_strlen($token2, '8bit');
    $n2 = mb_strlen($mask2, '8bit');
    if ($n1 > $n2) {
        $mask2 = str_pad($mask2, $n1, $mask2);
    } elseif ($n1 < $n2) {
        $token2 = str_pad($token2, $n2, $n1 === 0 ? ' ' : $token2);
    }
    $token2 = $token2 ^ $mask2;    
    
    
    $_csrfToken = str_replace('+', '.', base64_encode($mask . $token2));	
    //echo $_csrfToken;
    return $_csrfToken;
}