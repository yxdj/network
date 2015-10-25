<?php
/**
 * @link https://github.com/yxdj
 * @copyright Copyright (c) 2014 xuyuan All rights reserved.
 * @author xuyuan <1184411413@qq.com>
 */

//namespace yxdj\network;

/**
 * ```php
 * //GET请求
 * $http->get(string $url[, array $get[, array $cookie]]);
 * 
 * //HEAD请求
 * $http->head(string $url[, array $get[, array $cookie]]);
 * 
 * //POST请求
 * $http->post(string $url[, array $post[, array $cookie[, array $file]]]);
 * 
 * //自定义请求
 * $http->request([
 *     //必需参数
 *     'url'=>'http://example.com/path/to/test.php',
 * 
 *     //基础参数（可选）
 *     'method' => 'POST',
 *     'row' => ['Accept-Encoding'=>'gzip, deflate',...],    
 *     'get' => ['get_name'=>'get_value',...],
 *     'post' => ['post_name'=>'post_value',...],
 *     'cookie' => ['cookie_name'=>'cookie_value',...],    
 *     'file' => [
 *                 'file_name'=>['name'=>'filename','value'=>'filevalue']
 *                 ...
 *               ],
 *     
 *     //高级参数（可选）
 *     'ip' => [],              //访问域名所对应主机的ip,数组表示多ip,可随机发送，设置将省去DNS解析时间
 *     'allow'=>[],             //可允许的响应码，为空表示所有,返回一个不允许的响应码会抛出一个可捕获的异常
 *     'jump'=>-1,              //302,301响应码跳转几次，-1表示不跳转
 *     'ctimeout' => 15,        //连接超时（s）
 *     'atimeout' => 15,        //访问超时（s）
 *     'request' =>'',          //要发送的请求，此参数设置后基础参数中的所有设定失效
 * ]);
 *
 *
 * 获取响应：
 * $http->getCode();            //获取响应码
 * $http->getRequest();         //获取请求头
 * $http->getResponse();        //获取响应头
 * $http->getDebug();           //获取调试信息
 * $http->isTimeout();          //判断请求中是否有连接或访问超时
 * $http->getMessage();         //获取执行流程中的最后一条记录信息
 * $http->getCharset();         //获取访问链接的编码
 * $http->getKeyword();         //获取访问链接的关键字
 * $http->getContent();         //获取响应体
 * $http->a(false);             //获取响应体中的所有a链接,参数表示是否返回绝对路径
 * $http->img(false);           //获取响应体中的img链接,参数表示是否返回绝对路径
 * ```
 * 
 *
 * code
 * 含义:
 * 准备：1**
 * 正常：200，未完善2**
 * 跳转：301，302
 * 请求异常：4**
 * 服务端异常：5**
 *
 * 自定义:
 * 开始     900
 * 重置参数 901
 * 解析URL  902
 * 解析域名 903    
 * 设定请求 904     
 * 建立连接 905
 * 写入请求 906
 * only请求 916
 * 读取头部 907
 *
 */
class FlyHttp
{

    /**
     * connection timeout
     * 与服务器建立连接的最大等待时间（秒）
     */
    public $ctimeout = 15;

    /**
     * ask timeout
     * 向建立的连接读写数据的最大等待时间（秒）
     */
    public $atimeout = 15;    

    /**
     * 是否本地DNS解析
     * 关于ip设置最后选用的方案：
     * 起初通过注入一个方法，该方法通过解析域名和配置文件，解析出结果ip
     * 由于这种方式通用性较差，稳定性不好，所以干脆直接设置ip
     * 如果需要自动解析域名，可在调用处通过自定义的方法处理出结果后，传入ip即可
     * 格式：['ip' => '127.0.0.1'], 或 ['ip' => ['127.0.0.1', '*.*.*.*']]
     */
    public $ip;
  
    /**
     * 只有-1才得以初始化参数
     * 关于该参数的详细说明（不需要用跳转不设该参数即可）：
     * 假设跳转次数为2的话，跳1次变为1去重新请求，再跳一次变为0去重新请求
     * 由于每次发送新请求（非跳转）都要重置部分参数，但跳转请求又不能重置这部分参数
     * 因此设定jump值来标示状态，当跳转次数表示为具体次数时，最后一次置为0还需保留参数
     * 所以将jump为-1设为可重置新求的状态，
     * 在每次的over()方法里设置jump为-1，以便下次请求参数得以重置
     */
    public $jump = -1 ;
    
    
    /**
     * 可以接收的响应码
     */
    public $allow = array();    
    
    /**
     * 默认请求头行信息
     */
    public $method;
    
    /**
     * 请求路径
     */
    public $path;
    
    /**
     * 请求HTTP版本
     */
    public $version;
                  

    /**
     * 默认请求头域信息
     */
    public $row;

    /**
     * 请求GET参数
     */
    public $get;
    
    /**
     * 请求POST参数
     */
    public $post;   

    /**
     * 请求COOKIE参数
     */
    public $cookie;
    
    /**
     * 请求发送文件参数
     */
    public $file;
    
    /**
     * 仅发送请求
     */
    public $onlyRequest = false;
    

//---------请求过程中生成-------------------------------------------
    /**
     * 是否超时
     */
    private $timeout = false;

    /**
     * 请求是否需要发生跳转，内部生成，内部使用
     */
    public $over = false;    
    
    /**
     *解析后的URL信息
     */
    private $urls; 
    
    /**
     * 执行状态信息
     */
    private $message;

    /**
     * 执行流程详情
     */
    private $infos;
    /**
     * 响应码
     */
    private $code = 900;

    /**
     * 文档编码
     */
    private $charset;

    /**
     *文档关键字
     */
    private $keyword;

    /**
     * 跳转地址
     */
    private $location;

    /**
     * 请求头
     */
    private $request;

    /**
     * 响应头
     */
    private $response;

    /**
     * 响应内容
     */
    private $content;

    /**
     * 采集开始时间
     */
    private $startTime;

    /**
     * 每一过程起始进间
     */
    private $startTime2;





    
    /**
     * 构造函数
     * 写请求参数也是白写，请求时会重置参数(请求就是这样)，
     * 所以这里只写全局的构造参数，目前没有。
     */
    public function __construct($config=array())
    {
        $this->setConfig($config); 
    }
    
    
    /**
     * 设置http配置
     *
     * 有一个能数比较特别$row,但它还是属于请求参数，对它初始化之后像里合并
     */
    public function setConfig($config=array())
    {
        foreach($config as $key => $value){
            $this->$key = $value;
        }    
    }
    



    
    /**
     * 发送POST请求
     */
    public function post($url, $post=null, $cookie = null, $file = null)
    {
        $config = array(
            'method' => 'POST',
            'url' => $url,
            'post' => $post,
            'cookie' => $cookie,
            'file' => $file,
        );
        return $this->request($config);
    }    


    /**
     * 发送GET请求
     */    
    public function get($url, $get=null, $cookie=null)
    {
        $config = array(
            'method' => 'GET',
            'url' => $url,
            'get' => $get,
            'cookie' => $cookie,
        );
        return $this->request($config);
    }  

    /**
     * 发送HEAD请求
     */
    public function head($url, $get=null, $cookie=null)
    {
        $config = array(
            'method' => 'HEAD',
            'url' => $url,
            'get' => $get,
            'cookie' => $cookie,
        );
        return $this->request($config);
    } 
    
    /**
     * 传入配置，对请求各项参数配置
     * 可以直接传入原始请求信息，直接发送
     */
    public function request($config=array())
    {
        //响应码
        $code = $this->run($config);
        
        //允许的响应码
        if (!is_array($this->allow)) {
            $this->allow = array($this->allow);
        }
        
        //这里是方便调试。
        //响应确认检查content为主，其它的只是辅助。
        //响应200及指定的头域，content不合法也没用。
        //应该统一检查content,再根据情况判断其它
        if (in_array($code, $this->allow) || empty($this->allow)) {
            return $this;
        } else {
            throw new \Exception('not allow code: '.$code);
        }           
    }

    /**
     * 执行请求
     */
    private function run($config)
    {
        //开始 900
        $go = true;
        //重置参数 901
        $go ? $go = $this->resetRequest($config) : null;
        //解析URL 902
        $go ? $go = $this->parseUrl() : null;
        //解析域名 903    
        $go ? $go = $this->parseDomain() : null;
        //设定请求 904     
        $go ? $go = $this->setRequest() : null;
        //建立连接 905
        $go ? $go = $this->connect() : null;
        //写入请求 906
        $go ? $go = $this->writeRequest() : null;
        //读取头部 907
        $go ? $go = $this->readResponse() : null;
        //读取全文   
        $go ? $go = $this->readContent() : null;
        //关闭连接
        $go ? $go = $this->close() : null;
        //结束
        $this->over($go);
        //返回
        return $this->code;    
    }   
    
    /**
     * 重置请求
     */
    private function resetRequest($config)
    {
        //执行这里，是为了清除上次执行照成的影响
        //但这些操作在执行跳转时，又还需要继续使用(直到1->0),
        //over时变为-1,下次请求就可以执行了
        if ($this->jump < 0) {
            //设定启动时间
            $this->startTime2 = $this->startTime = microtime(true);
     
            $this->allow = [];
            $this->infos = array();//执行过程详情
            $this->over = false;//还未执行over()

            $this->ctimeout = 15;
            $this->atimeout = 15;
            $this->method = 'POST';
            $this->path = '/';
            $this->version = 'HTTP/1.1';
            $this->row = array(
                'Host'=>'localhost',
                'User-Agent'=>'Mozilla/5.0 (Windows NT 6.1; WOW64; rv:35.0) Gecko/20100101 Firefox/35.0',
                'Connection'=>'Close'
                );
            $this->url = '';
            $this->get = array();
            $this->post = array();
            $this->cookie = array();
            $this->file = array();
        }

        //以下信息不管是重新请求，还是跳转都会执行
        $this->message = '';//执行过程最后一步
        $this->urls = array(); //通过url得到的信息
        $this->code = 901;
        $this->charset = '';
        $this->keyword = '';
        $this->location = '';
        $this->request = '';
        $this->response = '';
        $this->content = '';
        $this->ip = '';
        $this->timeout = false;
        
        //仅在重新请求时执行一次，跳转时仅有url参数了
        if (isset($config['row'])) {
            $this->row = array_merge($this->row, $config['row']);
            unset($config['row']);
        }

        //设置参数(重新请求时执行一次，跳转会重新配置url)
        $this->setConfig($config);

        //一定要有url选项
        if (empty($this->url)) {
            $this->record('resetRequest','Http::url require!');
            return false;
        }
        
        $this->record('resetRequest','ok');        
        return true;
    }    

    /**
     * 解析URL
     */
    private function parseUrl()
    {
        $this->code = 902;
        $url = $this->url;
        if (!preg_match('/^(\w*):\/\//i',$url, $match)) {
            $url='http://'.$url;
        } elseif ($match[1] != 'http') {
            $this->record('parseUrl','ng(not http => '.$url.')');
            return false;    
        }
        $urls= parse_url($url); 
        !isset($urls['scheme']) && $urls['scheme'] = 'http';     //获取协议
        !isset($urls['host']) && $urls['host'] = '';                //获取主机
        !isset($urls['path']) && $urls['path'] = '/';           //获取路径
        !isset($urls['query']) && $urls['query'] = '';           //获取参数
        !isset($urls['port']) && $urls['port'] = '80';             //获取端口    

        //添加GET参数 
        if (count($this->get)>0){
            parse_str($urls['query'],$output);//解析字符串为数组
            $output=array_merge($output,$this->get);//添加想要的参数
            $urls['query']=trim(http_build_query($output));//重新生成查询字符串            
        }

        $urls['paths'] = $urls['path'].($urls['query'] ? '?'.$urls['query'] : ''); //组拼完整路经                  
        $this->urls=$urls;
        $this->record('parseUrl','ok('.$url.')');
        return true;
    }

    /**
     * 解析域名
     */
    private function parseDomain()
    {
        //是否启动本地域名解析
        //$this->urls['ip']='121.10.139.10';
        $this->code = 903;
        if (!empty($this->ip)) {
            if (is_array($this->ip)) {
                $key = array_rand($this->ip);
                $this->urls['ip'] = $this->ip[$key];
            } else {
                $this->urls['ip'] = $this->ip;
            }
        }else{
            $this->urls['ip'] = trim(gethostbyname($this->urls['host']));    //自动获取IP        
        }
        $this->record('parseDomain','ok('.$this->urls['ip'].')');
        return true;
    }



    
    /**
     * 设定请求
     */
    private function setRequest()
    {
        $this->code = 904;
        if (!empty($this->request)) {
            $this->record('setRequest','ok(without parser)');
            return true;
        }
        //准备部分的请求头    
        //$port=$this->urls['port']=='80'?'':':'.$this->urls['port'];
        //$this->row['Host']=$this->urls['host'].$port; 

        $this->row['Host']=$this->urls['host'];        
        $this->path = $this->urls['paths'];
        //请求行
        $line = $this->method.' '.$this->path.' '.$this->version."\r\n";        

        //POST值
        $post='';
        if ($this->method == 'POST') {
            //只要有文件，就按文件处理
            if (count($this->file) > 0) {
            
                $bound='yxdj'.mt_rand();
                $str='';
                
                //一般POST数据
                if (count($this->post) > 0) {
                    $post_query = http_build_query($this->post);
                    $posts = explode('&', $post_query);
                    foreach($posts as $value){
                        list($key, $val) = explode('=', $value);
                        $key = urldecode($key);
                        $val = urldecode($val);
                        $str .= '--'.$bound."\r\n";
                        $str .= "Content-Disposition: form-data; name=\"{$key}\"";
                        $str .= "\r\n";
                        $str .= "Content-Type: text/plain; charset=UTF-8";
                        $str .= "\r\n";
                        $str .= "Content-Transfer-Encoding: 8bit";
                        $str .= "\r\n\r\n";
                        $str .= $val;
                        $str .= "\r\n";
                    }
                }
                
                //文件POST
                foreach($this->file as $key => $value){
                    $name = empty($value['name']) ? $key : $value['name'];
                    $type = empty($value['type']) ? 'application/octet-stream' : $value['type'];
                    $str .= '--'.$bound."\r\n";
                    $str .= "Content-Disposition: form-data; name=\"{$key}\"; filename=\"{$name}\"";
                    $str .= "\r\n";
                    $str .= "Content-Type: {$type}";
                    $str .= "\r\n";
                    $str .= "Content-Transfer-Encoding: binary";
                    $str .= "\r\n\r\n";
                    $str .= $value['value'];
                    $str .= "\r\n";
                }
                $str.='--'.$bound.'--';
                $length=strlen($str);
                $post=$str;
                
                $this->row['Content-Type']='multipart/form-data; boundary='.$bound;
                $this->row['Content-Length']=$length;            
            }else{
                //是否有一般post的值
                if ( count($this->post) > 0 ) {
                    $post=http_build_query($this->post);
                    $length=strlen($post);
                    $this->row['Content-Type']='application/x-www-form-urlencoded';
                    $this->row['Content-Length']=$length;
                }
            }
            //非post请求，不需要处理
        }

        /**
         * COOKIE值
         */
        $cookie='';
        if ( count($this->cookie) > 0 ) {
            $cookie = 'Cookie: ';
            $cookies = http_build_query($this->cookie);
            $cookies = str_replace('&', '; ', $cookies);
            $cookie = $cookie . $cookies . "\r\n";
            /*
            foreach ( $this->cookie as $cookieKey => $cookieVal ) {
                $cookie.= $cookieKey."=".urlencode($cookieVal)."; ";
            }
            $cookie= substr($cookie,0,-2) . "\r\n";
            */
        }
        
        //请求域
        $row='';
        if(count($this->row) > 0 ){
            foreach($this->row as $rowKey => $rowVal)
                $row .= $rowKey.": ".$rowVal."\r\n";
        }
        

        $headers=$line.$row.$cookie."\r\n".$post;
        
        $this->request = $headers;
        $this->record('setRequest','ok');
        return true;
    }


    
    /**
     * 建立连接
     */
    private function connect()
    {
        $this->code = 905;
        $fp=@fsockopen($this->urls['ip'], $this->urls['port'], $errno, $erron, $this->ctimeout);
        if(!$fp){
            $this->record('connect','ng('.$errno.')');
            //connect time out!
            if($errno == 10060){
                $this->timeout = true;
            }
            
            return false;
        }

        //设定参数
        stream_set_blocking($fp, true);//设置为阻塞模式
        stream_set_timeout($fp, $this->atimeout);//设置超时
        $this->fp=$fp;
        $this->record('connect', 'ok');
        return true;
    }

    /**
     * 写入请求头
     */
    private function writeRequest()
    {
        $this->code = 906;
        $write=fwrite($this->fp, $this->request);
        if(!$write){
            $this->record('writeRequest', 'ng');
            @fclose($this->fp);
            return false;
        }    
        $this->record('writeRequest', 'ok');
        
        if ($this->onlyRequest) {
            $this->code = 916;
            @fclose($this->fp);
            $this->record('onlyRequest', 'ok');
            return false;
        }
        
        return true;
    }
    
    /**
     * 获取HTTP头
     */
    private function readResponse()
    {
        $this->code = 907;
        //确认资源句柄
        if(!is_resource($this->fp)){
            $this->record('readResponse', 'no resource handle!');
            return false;
        }    

        //读取HTTP头
        $header = '';
        while (!feof($this->fp)) {
            $headerRow = fgets($this->fp);
            if ($this->checkTimeout($this->fp)) {//是否超时后进来
                $this->record('readResponse','read time out(http)!');
                $this->timeout = true;
                @fclose($this->fp);
                return false;
            }
      
            //匹配响应状态
            if (preg_match("/^HTTP\/[^\s]+\s+([^\s]+)\b/", trim($headerRow), $status)) {
                $this->code = (int)$status[1];                
            }

            //处理301，302 获取跳转地址
            if (preg_match("/^(Location:|URI:)\s*(.*)/i",trim($headerRow), $location)) {
                $this->location = $location[2];    
            }
              
            
            if (trim($headerRow) == '') {
                break;
            } else {
                $header .= $headerRow;
            }

        }
        $this->response = $header;

        if ($this->jump > 0 &&($this->code=='301' || $this->code=='302') && $this->location) {
                $this->jump--; 
                $this->record('readResponse','code: ' . $this->code . ' ' . $this->location);
                $this->request(array('url' => $this->location));
                return false;
        }
        
        $this->record('readResponse','code: '.$this->code.' '.$this->location);
        return true;
        

    }

    /**
     * 获取HTTP响应体
     */
    private function readContent()
    {
        //确认资源句柄
        if(!is_resource($this->fp)){
            $this->record('readContent','no resource handle!');
            return false;
        }

        //HEAD请求直接返回
        if ($this->method == 'HEAD') {
            $this->record('readContent','HEAD REQUEST');        
            return true;
        }
        
        //取回正文信息
        $content='';
        while (!feof($this->fp)) {
            if($this->checkTimeout($this->fp)){
                $this->record('readContent','read time out(html)!');
                $this->timeout = true;
                @fclose($this->fp);
                return false;
            }
            $content .= fread($this->fp, 512);
        }        
        $this->content=$content;
        $content=trim($content);
        $this->record('readContent','ok');        
        return true;
    }
    
    /**
     * 关闭连接
     */
    private function close()
    {
        //结束
        @fclose($this->fp);    
        $this->record('close','ok');
        return true;
    }

    /**
     * 记录运行过程
     */
    private function record($name,$message)
    {
        $this->message=$name.'=>'.$message;
        $this->infos[]=array('name'=>$name,'msg'=>$message,'time'=>$this->difTime());
    }
    
    /**
     * 计算请求时间差
     */
    private function difTime($start=null,$end=null)
    {
        if(!$start) $start=$this->startTime2;
        if(!$end) $end=microtime(true);
        $dif=round(($end-$start),4);
        $this->startTime2=$end;
        return $dif.'s';
    }    
    
    /**
     * 记录请求结束
     */
    private function over($go)
    {
        if ($this->over) {
            return false;
        }
        $this->endTime = microtime(true);//设定结束时间
        $start = date('Y-m-d H:i:s', $this->startTime);
        $end = date('Y-m-d H:i:s', $this->endTime);
        $this->infos[] = array(
            'name' => 'over('.$this->code.')',
            'msg' => $start.'->'.$end,
            'time' => $this->difTime($this->startTime, $this->endTime),
        );
        $this->jump = -1;
        $this->over = true;  
    }
    
    /**
     * 检查数据读取超时与否
     */
    private function checkTimeout($fp)
    {
        //读取失败，检测读取状态 
        $info = stream_get_meta_data($fp);       
        if ($info['timed_out']) {
            return true;
        }            
        return false;
    }

    /**
     * 是否访问超时
     */
    public function isTimeout()
    {
        return $this->timeout;
    }

    
    /**
     * 获取调试信息 
     */
    public function getDebug($content=false, $direct=false)
    {
        $info='';
        $info .= "(request)\r\n"
              . $this->request
              . "\r\n\r\n(response)\r\n"
              . $this->response;
        if ($content) {
            $info .= "\r\n\r\n(content)\r\n"
                  . $this->content;
        }
        $info .= "\r\n\r\n(recode)\r\n";
        foreach ($this->infos as $key => $value) {
            $info .= str_pad($value['name'] . ': ' . $value['msg'], 60)
                  . '|'
                  . $value['time']
                  . "\r\n";
        }
        if ($direct) {
            return $info;
        } else {
            return PHP_SAPI == 'cli' ? $info :  "<pre style=\"background:#000;color:#fff;\">\r\n$info</pre>";//preg_replace('/(?<!\<br) /','&nbsp;',nl2br($info));            
        }
    }

    /**
     * 执行流程
     */
    public function getRecord($direct=false)
    {
        $info = '';
        foreach ($this->infos as $key => $value) {
            $info .= str_pad($value['name'] . ': ' . $value['msg'], 60)
                  . '|'
                  . $value['time']
                  . "\r\n";
        }
        if ($direct) {
            return $info;
        } else {
            return PHP_SAPI == 'cli' ? $info :  "<pre style=\"background:#000;color:#fff;\">\r\n$info</pre>";//preg_replace('/(?<!\<br) /','&nbsp;',nl2br($info));            
        }        
    } 

    
    /**
     * 获取响应码
     */
    public function getCode()
    {
        return $this->code;
    }

    /**
     * 获取请求头
     */
    public function getRequest()
    {
        return $this->request;
    }
    
    /**
     * 获取响应头
     */
    public function getResponse()
    {
        return $this->response;
    }
    
    /**
     * 获取响应内容
     */
    public function getContent($do = false)
    {
        if ($do) {
            //return http_chunked_decode($this->content);
            return $this->unchunk2($this->content);
        } else {
            return $this->content;
        }
    }

    /**
     * 获取执行状态信息
     */
    public function getMessage()
    {
        return $this->message;
    }   
    
    /**
     * 获取文档编码
     */
    public function getCharset()
    {
        if (!empty($this->charset)) return $this->charset;
        $charset = array('utf-8','gbk','gb2312');
        $reg='/' . implode('|', $charset) . '/i';
        if ($value = preg_match($reg, $this->response . $this->content, $arr)) {
            $charset = strtolower($arr[0]);
        } else {
            $charset = '';
        }
        $this->charset = $charset;
        return $this->charset;
    }

    /**
     * 从网页头信息中找出关键字
     */
    public function getKeyword()
    {
        if(!empty($this->keyword)) return $this->keyword;
        if(preg_match_all("
                        /<\s*meta\s.*?(keywords|other).*?content\s*=\s*        #查找标识
                        ([\"\'])?                                            #是否有前引号
                        (?(2) (.*?)\\2 | ([^\s\>]+))                        #根据是否有前引号匹配内容
                        /isx",$this->content,$keywords,PREG_PATTERN_ORDER)){                    
        $keyword=implode(',',$keywords[3]);
        }else if(preg_match("/<\s*title\s*>(.*?)<\s*\/\s*title\s*>/is",$this->content,$keywords)){
            $keyword=$keywords[1];
        }else{
            $keyword='';
        }
        $this->keyword=$keyword;
        return $this->keyword;
    }

    
    /**
     * 从给定内容中取得所有a标签链接
     */
	public function a($real = false) {
		$match=array();
		preg_match_all("'<\s*a\s.*?href\s*=\s*([\"\'])?(?(1) (.*?)\\1 | ([^\s\>]+))'isx", $this->content, $links); 
		// catenate the non-empty matches from the conditional subpattern
		while (list($key, $val) = each($links[2])) {
			if (!empty($val))
				$match[] = $val;
		} while (list($key, $val) = each($links[3])) {
			if (!empty($val))
				$match[] = $val;
		} 
		$match=array_unique($match);//去除相同
        
        if ($real) {
            $match = self::realPath($match);
        }        
        
		// return the links
		return $match;
	}

    /**
     * 从给定内容中取得所有img标签链接
     */
	public function img($real = false) {
		$match=array();
		preg_match_all("'<\s*img\s.*?src\s*=\s*([\"\'])?(?(1) (.*?)\\1 | ([^\s\>]+))'isx", $this->content, $links); 
		// catenate the non-empty matches from the conditional subpattern
		while (list($key, $val) = each($links[2])) {
			if (!empty($val))
				$match[] = $val;
		} 
		while (list($key, $val) = each($links[3])) {
			if (!empty($val))
				$match[] = $val;
		} 
		$match=array_unique($match);//去除相同
        
        
        if ($real) {
            $match = $this->realPath($match);
        }
        
		// return the links
		return $match;
	}

    /**
     * 拼接路径完整性
     */
	private function realPath($url){

        $scheme = $this->urls['scheme'].'://'; 
		$host=$this->urls['host'];
		$port = $this->urls['port']=='80'?'':':'.$urls['port'];		
		
        $path=$scheme.$host.$port;//绝对路径
		$path2=$path.$this->urls['path'];
        
		$path2=substr($path2,-1)=='/'?$path2:dirname($path2);//以'/'结束直接以此为相对路径，否则上一级
		$path2=substr($path2,-1)=='/'?$path2:$path2.'/';//最后以'/'结束
		 
		$url=$this->runPath($url,$path,$path2);	
		return $url;
	}
	
	/**
     * 拼接路径完整性2,递规
     */
	private function runPath($str1,$path,$path2){
		if(is_array($str1)){
			$urls=array();
			foreach($str1 as $key => $value){
				$urls[$key]=self::runPath($value,$path,$path2);
			}
			return $urls;
		}

		if(is_string($str1)){
			if(preg_match('/^[a-z]{1,10}:\/\//i',dirname($str1))){
				return $str1;
			}
			
			if(substr($str1,0,1)=='/'){
				return $path.$str1;
			}
			
			if(substr($str1,0,1)!='.'){
				return $path2.$str1;
			}
			
			if(substr($str1,0,2)=='./'){
				return $path2.substr($str1,2);
			}  
			
			if(substr($str1,0,3)=='../'){
				while(substr($str1,0,3)=='../'){
					$str1=substr($str1,3);
					$path2=dirname($path2);			
				}
				return $path2.'/'.$str1;
			}
			return $str1;
		}
	} 


	//过滤文档长度标识
	private function unchunk($result) {
		return preg_replace_callback(
			'/(?:(?:\r\n|\n)|^)([0-9A-F]+)(?:\r\n|\n){1,2}(.*?)'.
			'((?:\r\n|\n)(?:[0-9A-F]+(?:\r\n|\n))|$)/si',
			create_function(
				'$matches',
				'return hexdec($matches[1]) == strlen($matches[2]) ? $matches[2] : $matches[0];'
			),
			$result
		);
	}
    
    //过滤文档长度标识
    private function unchunk2($chunk) {
        if (!function_exists('http-chunked-decode')) {
            $pos = 0;
            $len = strlen($chunk);
            $dechunk = null; 
            while(($pos < $len)
                && ($chunkLenHex = substr($chunk,$pos, ($newlineAt = strpos($chunk,"\n",$pos+1))-$pos)))
            {
                if (!$this->is_hex($chunkLenHex)) {
                    //trigger_error('Value is not properly chunk encoded', E_USER_WARNING);
                    return $chunk;
                }
                $pos = $newlineAt + 1;
                $chunkLen = hexdec(rtrim($chunkLenHex,"\r\n"));
                $dechunk .= substr($chunk, $pos, $chunkLen);
                $pos = strpos($chunk, "\n", $pos + $chunkLen) + 1;
            }
            return $dechunk;
        } else {
            return http_chunked_decode($result);
        }
    }

    private function is_hex($hex) {
        $hex = strtolower(trim(ltrim($hex,"0")));
        if (empty($hex)) { $hex = 0; };
        $dec = hexdec($hex);
        return ($hex == dechex($dec));
    }
}