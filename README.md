

HTTP工具包：请求调试，API客户端，网页采集
=========================================


##一、功能说明：
> 这是一个http工具包,你可以将它用作：  
> 1. 请求调试;  
> 2. API客户端；  
> 3. 网页采集；  


> 特点：  
> 请求传值：请求头域，GET参数，POST参数，COOKIE参数，及FILE参数，或是直接发送http请求头  
> 调试信息：从请求的发起到响应结束，整个过程都有完整调试信息记录。  
> 超时控制：连接超时，访问超时  
> 页面跟踪：301，302  
> 指定连接IP: 可免去DNS对域名解析,设定多个将随机选取  
> 网页解析：关键字，编码，链接  



##二、安装/删除：

> 此工具要求php5.3以上，不需要核心以外的扩展，下文使用说明是基于5.4的语法操作（主要是数组表示）  
> 此工具是作为一个composer包发布，但是核心文件Http.php并不对其它文件依赖，可以将其独立出来使用  


> 安装：composer require yxdj/network  
> 删除：composer remove yxdj/network  



##三、使用说明($http表示请求对象)
###$http的获取:

> 方法一：`$http = new \yxdj\network\Http();`  
> 方法二：`$http = \yxdj\network\Api::gethttp();  //对方法一的调用，并保持单例`

###$http的操作:

####发送请求：


```php
//GET请求
$http->get(string $url[, array $get[, array $cookie]]);

//HEAD请求
$http->head(string $url[, array $get[, array $cookie]]);

//POST请求
$http->post(string $url[, array $get[, array $cookie[, array $file]]]);

//自定义请求
$http->request([
    //必需参数
    'url'=>'http://example.com/path/to/test.php',

    //基础参数（可选）
    'method' => 'POST',
    'row' => ['Accept-Encoding'=>'gzip, deflate',...],    
    'get' => ['get_name'=>'get_value',...],
    'post' => ['post_name'=>'post_value',...],
    'cookie' => ['cookie_name'=>'cookie_value',...],    
    'file' => [
                'file_name'=>['name'=>'filename','value'=>'filevalue']
                ...
              ],
    
    //高级参数（可选）
    'ip' => [],        //访问域名所对应主机的ip,数组表示多ip,可随机发送，设置将省去DNS解析时间
    'allow'=>[],       //可允许的响应码，为空表示所有,返回一个不允许的响应码会抛出一个可捕获的异常
    'jump'=>-1,        //302,301响应码跳转几次，-1表示不跳转
    'ctimeout' => 15,  //连接超时（s）
    'atimeout' => 15,  //访问超时（s）
    'request' =>'',    //要发送的请求，此参数设置后基础参数中的所有设定失效
]);
```

> 注意：  
> 上述url参数是必需的，其它可选  
> $get,$cookie,$post是名值对数组，可以是多维的  
> $file，示例：['myfile'=>['name'=>'文件名字','value'=>'文件内容'],...],  
  php服务端可以通过$_FILES['myfile']获取上述文件   
> get/post/head这3个方法是对rquest方法的简化，它们的返回仍是对象$http,但其中已有响应结果。    
> request的具体处理过程：  
> 1. 清除$http中上次的请求内容；  
> 2. 重新写入请求配置信息和获取的响应;  
> 3. 返回的$http可继续做获取响应操作  


####获取响应：

```php
//debug信息，包括请求头，响应头，及具体的解析过程
//$content表示是否输出响应体
//$direct表示是否直接输出信息，默认在web模式下格式化
$http->getDebug([$content=false[, $direct=false]])

//响应码，如果是大于或等于900的响应码将是请求类自定义的
$http->getCode()

//请求头信息
$http->getRequest()

//响应头信息
$http->getResponse()

//响应主体
$http->getContent()

//html关键字（如果能解析到）
$http->getKeyword()

//响应的编码（如果能解析到）
$http->getCharset()

//响应体中a标签的链接（如果能解析到）
$http->a()

//响应体中img标签的链接（如果能解析到）
$http->img()

//是否请求超时
$http->isTimeout()
```


##四、使用示例:

###GET请求

```php
$http = Api::getHttp()->get('http://php.net');

//(output)PHP: Hypertext Preprocessor
echo $http->getKeyword();

//(output)utf-8
echo $http->getCharset();
```


###自定义请求
```php
//server: http://test/test.php
<?php
print_r($_GET);

print_r($_POST);

print_r($_COOKIE);

print_r($_FILES);
```


```php
//client
$http = Api::getHttp()->request([
    'method' => 'POST',
    'url' => 'http://test/test.php',
    'get' => ['get1'=>'param2', 'get2'=>['a'=>'param2a','b'=>'param2b']],
    'post' => ['post1'=>'param2', 'post2'=>['a'=>'param2a','b'=>'param2b']],
    'cookie' => ['cookie1'=>'param2', 'cookie2'=>['a'=>'param2a','b'=>'param2b']],
    'file' => [
        'file1' => ['name'=>'111.txt','value'=>'123456'],
        'file2[a]' => ['name'=>'aaa.xxx','value'=>'xxxxxx'],
        'file2[b]' => ['name'=>'bbb.yyy','value'=>'yyyyyy'],
    ],
]);
echo $http->getDebug(true);

/*
(output)
(request)
POST /test.php?get1=param2&get2%5Ba%5D=param2a&get2%5Bb%5D=param2b HTTP/1.1
Host: test
User-Agent: Mozilla/5.0 (Windows NT 6.1; WOW64; rv:33.0) Gecko/20100101 Firefox/33.0
Connection: Close
Content-Type: multipart/form-data; boundary=yxdj274972258
Content-Length: 976
Cookie: cookie1=param2; cookie2%5Ba%5D=param2a; cookie2%5Bb%5D=param2b

--yxdj274972258
Content-Disposition: form-data; name="post1"
Content-Type: text/plain; charset=UTF-8
Content-Transfer-Encoding: 8bit

param2
--yxdj274972258
Content-Disposition: form-data; name="post2[a]"
Content-Type: text/plain; charset=UTF-8
Content-Transfer-Encoding: 8bit

param2a
--yxdj274972258
Content-Disposition: form-data; name="post2[b]"
Content-Type: text/plain; charset=UTF-8
Content-Transfer-Encoding: 8bit

param2b
--yxdj274972258
Content-Disposition: form-data; name="file1"; filename="111.txt"
Content-Type: application/octet-stream
Content-Transfer-Encoding: binary

123456
--yxdj274972258
Content-Disposition: form-data; name="file2[a]"; filename="aaa.xxx"
Content-Type: application/octet-stream
Content-Transfer-Encoding: binary

xxxxxx
--yxdj274972258
Content-Disposition: form-data; name="file2[b]"; filename="bbb.yyy"
Content-Type: application/octet-stream
Content-Transfer-Encoding: binary

yyyyyy
--yxdj274972258--

(response)
HTTP/1.1 200 OK
Date: Thu, 05 Mar 2015 07:43:07 GMT
Server: Apache/2.4.9 (Win64) PHP/5.5.12
X-Powered-By: PHP/5.5.12
Content-Length: 1393
Connection: close
Content-Type: text/html


(content)
Array
(
    [get1] => param2
    [get2] => Array
        (
            [a] => param2a
            [b] => param2b
        )

)
Array
(
    [post1] => param2
    [post2] => Array
        (
            [a] => param2a
            [b] => param2b
        )

)
Array
(
    [cookie1] => param2
    [cookie2] => Array
        (
            [a] => param2a
            [b] => param2b
        )

)
Array
(
    [file1] => Array
        (
            [name] => 111.txt
            [type] => application/octet-stream
            [tmp_name] => D:\WAMP\wamp\tmp\php6099.tmp
            [error] => 0
            [size] => 6
        )

    [file2] => Array
        (
            [name] => Array
                (
                    [a] => aaa.xxx
                    [b] => bbb.yyy
                )

            [type] => Array
                (
                    [a] => application/octet-stream
                    [b] => application/octet-stream
                )

            [tmp_name] => Array
                (
                    [a] => D:\WAMP\wamp\tmp\php609A.tmp
                    [b] => D:\WAMP\wamp\tmp\php609B.tmp
                )

            [error] => Array
                (
                    [a] => 0
                    [b] => 0
                )

            [size] => Array
                (
                    [a] => 6
                    [b] => 6
                )

        )

)


(recode)
resetRequest: ok                                            |0s
parseUrl: ok(http://test/test.php)                          |0s
parseDomain: ok(127.0.0.1)                                  |0s
setRequest: ok                                              |0s
connect: ok                                                 |0.01s
writeRequest: ok                                            |0s
readResponse: code: 200                                     |0s
readContent: ok                                             |0s
close: ok                                                   |0s
over(200): 2015-03-05 15:43:07->2015-03-05 15:43:07         |0.01s

*/
```



###封装API

>  $http已经能简单的发送参数，并能方便的获取响应  
>  但在应用程序中使用它时，  
>  往往还需在请求前对参数过滤分析，调整为可供发送的格式  
>  在请求后还需对响应结果进行判断，解析，处理成最后需要的格式  
>  可以将这个过程封装成一个API，以便更简便的调用  

**服务端定义：用户登录验证**
```php
//server:http://test/login.php
<?php

$username = !empty($_POST['username']) && is_string($_POST['username'])?$_POST['username']:'';
$password = !empty($_POST['password']) && is_string($_POST['password'])?$_POST['password']:'';


if ($user = findUser($username, $password)) {
    $result = ['status' => 'ok', 'data' => $user];
} else {
    $result = ['status' => 'ng', 'error' => 'username or password error!'];
}

echo json_encode($result);


function findUser($username, $password){
    $users =[
                ['username'=>'test','password'=>md5('test_pwd'),'info'=>'test login success!'],
                ['username'=>'yxdj','password'=>md5('yxdj_pwd'),'info'=>'yxdj login success!'],
            ];
            
    $find = null;
    foreach ($users as $user) {
        if ($user['username'] == $username) {
            $find = $user;
            break;
        }
    }
    
    if ($find && $find['password'] == $password) {
        return $find;
    } else {
        return null;
    }

}
```

**客户端定义：请求发送与响应处理**
```php
//client
namespace yxdj\network\api;

use yxdj\network\Api;

class TestApi extends Api
{
    public static function login($data)
    {
        $url = 'http://test/login.php';
        $username = isset($data['username']) ? $data['username'] : '';
        $password = isset($data['password']) ? $data['password'] : '';
        $user = ['username' => $username, 'password' => md5($password)];
        $content = Api::getHttp()->post($url, $user)->getContent();
        $result = json_decode($content, true);
        if (isset($result['status'])) {
            if ($result['status'] == 'ok') {
                return $result['data']['info'];
            } elseif ($result['status'] == 'ng') {
                return $result['error'];
            }
        }
        
        //如有需要可以在此根据请求对象中的信息调试或判断处理
        return 'unknow error!';
    }
}
```

**TestApi::login()调用**
```php
use yxdj\network\api\TestApi;

//(output)yxdj login success!
echo TestApi::login(['username'=>'yxdj', 'password'=>'yxdj_pwd']);

//(output)username or password error!
echo TestApi::login(['username'=>'yxdj', 'password'=>'xxx']);
```



您如果对此工具有兴趣或疑问，欢迎与我联系。
---------------------------------------
