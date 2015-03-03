yxdj/network
http-tool:request api curl
HTTP工具包：请求调试，API客户端，网页采集
=====================================


功能说明：
这是一个http工具包,使用它你可以：

1.请求调试。
方便的向web服务器发起你需要的请求参数，所有这些都是一个组数表示，
包括：
请求头域，GET参数，POST参数，COOKIE参数，及FILE参数，或是直接发送http请求头
从请求的发起到响应结束，整个过程都有完整调试信息记录。

2.API客户端
如请求调试中所述，各种参数方便传输
xml,json,的解析

3.网页采集，
超时控制：连接超时，访问超时
页面跟踪：301，302
HTML关键字,编码，链接的抓取，多媒体数据直接读取
其它基于此的自定义功能



需要php版本5.3以上（以下为了表述方便，使用php5.4开始支持的"[]"替代"array()"）
不需要额外扩展，使用php核心函数fsockopen()发送请求


一、安装/删除：
安装：composer require yxdj/network
删除：composer remove yxdj/network


二、目录结构：
vendor
--yxdj
----network
------Http.php
------Api.php
------api
--------TestApi.php
------test.php
------composer.php
------README.md

三、使用说明($http表示请求对象)
1.获取$http请求对象：

    方法一：$http = new \yxdj\network\Http();
    方法二：$http = \yxdj\network\Api::gethttp();
    --如果要多次使用$http对象，不需每次都实例它，方法二是对方法一的调用，并保持单例

2.$http对象的方法:

    发送请求：
    
        ```php
        
        $http->get($url,$get=[],$cookie=[]);//GET请求
        $http->head($url,$get=[],$cookie=[]);//HEAD请求
        $http->post($url,$post=[],$cookie=[],$file=[]);//POST请求
        
        ```
        
        注意：
            上述$url是必需的
            $get,$cookie,$post是字符串名值对，可以是多维的
            $file，写个示例：['myfile'=>['name'=>'文件名字','value'=>'文件内容']]
            php服务端可以通过$_FILES['myfile']获取上述文件
        
        ```php
        
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
            'allow'=>[],       //可允许的响应码，为空表示所有
            'jump'=>-1,        //302,301响应码跳转几次，-1表示不跳转
            'ctimeout' => 15,  //连接超时（s）
            'atimeout' => 15,  //访问超时（s）
            'request' =>'',    //要发送的请求，此参数设置后基础参数中的所有设定失效
        ]);
        
        ```
        
        重要：
            get/post/head这3个方法是对rquest方法的简化，
            这3个方法将参数整理成数组再去调用request
            这3个方法的返回即是request的返回
            而request的返回的仍是当前对象$http,但它的内容已有变化了。
            request的具体处理过程：
            1.清除$http中上次请求的内容，把request得到的参数写入$http；
            2.发送请求并获取响应，此过程会生成一些参数并写入$http;
            3.然后返回的$http就可以做如下获取响应的操作了
        
        
    获取响应：
        //debug信息，包括请求头，响应头，及具体的解析过程
        $http->getDebug()
        
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
        
3.封装API
    $http已经将参数非常简单的做请求发送，并能方便的获取响应
    但在应用程序中使用它时，
    往往还需在请求前对参数过滤分析，调整为可供发送的格式
    在请求后还需对响应结果进行判断，解析，处理成最后需要的格式
    可以将这个过程封装成一个API，以便更简便的调用


四、使用示例:


use yxdj\network\Http:
$http = new Http();

发送请求
$http->get($url, $get=[], $cookie=[])
$http->head($url, $get=[], $cookie=[])
$http->post($url, $post=[], $cookie=[], $file=[])

$http->request([
    //必需参数
    'url'=>'http://example.com/path/to/test.php',

    //基础参数（可选）
    'method'=>'POST',
    'get'=>[],
    'post'=>[],
    'file'=>[],
    'row'=>[],
    
    //高级参数（可选）
    
])

//获取响应结果
$http->getDebug()
$http->getCode();
$http->getContent()
$http->getKeyword();
$http->getCharset();

//----------------------------------------------
//Api:
use yxdj\network\Api;
Api::getHttp();

//自定义：TestApi
use yxdj\network\api\TestApi;
TestApi::text();
TestApi::debug();






```php
echo Api::http([
    'url' => 'http://api.yii.app.com/login',
    'method' =>'POST',
    'get' => ['get'=>'aaaa'],
    'post' => ['post'=>'55555'],
    'cookie' => ['cookie'=>'555555'],
    'file' => [
                ['name'=>'file1','value'=>'33333'],
                ['name'=>'file2', 'value'=>'4444'],
                ['name'=>'file2', 'value'=>'5555'],
              ],
    //'request2' => $request,

]);
```


Installation
------------

The preferred way to install this extension is through [composer](http://getcomposer.org/download/).

Either run

```
php composer.phar require yxdj/network "*"
```

or add

```
"yxdj/network": "*"
```

to the require section of your `composer.json` file.

