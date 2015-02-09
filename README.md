yxdj/network for php
=====================================

get,post,cookie,file都支持数组请求，除了file都支持深维度数据（file本身没必要）
include:hostToIp<fun>,
HTTP控制类
$code=$http->getUrl()/postUrl()/headUrl();
if($code=='200'){
    $this->request/response/content/code;
    $this->getCharset()/getKeyword()/getDebug()
}

```php
use yxdj\network\Http;

$http = new Http();

$url = 'http://php.net';
if ($http->getUrl($url) == 200) {
    echo $http->getKeyword();
} else {
    echo $http->getDebug();
}
```
