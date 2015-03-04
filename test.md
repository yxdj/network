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
