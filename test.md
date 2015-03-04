yxdj/network
http-tool: debug,api,curl
HTTP工具包：请求调试，API客户端，网页采集
=====================================


##一、功能说明：
这是一个http工具包,使用它你可以：
###1.请求调试；
###2.API客户端；
###3.网页采集；


特点：
请求传值方便：请求头域，GET参数，POST参数，COOKIE参数，及FILE参数，或是直接发送http请求头
调试信息详细：从请求的发起到响应结束，整个过程都有完整调试信息记录。
超时控制：连接超时，访问超时
页面跟踪：301，302
HTML关键字,编码，链接的抓取



##二、安装/删除：
###安装：composer require yxdj/network
###删除：composer remove yxdj/network



##三、使用说明($http表示请求对象)
###1.获取$http请求对象:

> 方法一：$http = new \yxdj\network\Http();
方法二：$http = \yxdj\network\Api::gethttp();
--如果要多次使用$http对象，不需每次都实例它，方法二是对方法一的调用，并保持单例

###2.$http对象的方法:

发送请求：


```php
$http->get($url,$get=[],$cookie=[]);//GET请求
$http->head($url,$get=[],$cookie=[]);//HEAD请求
$http->post($url,$post=[],$cookie=[],$file=[]);//POST请求
```

注意：
    上述$url是必需的
    $get,$cookie,$post是字符串名值对数组，可以是多维的
    $file，示例：['myfile'=>['name'=>'文件名字','value'=>'文件内容'],...]
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
