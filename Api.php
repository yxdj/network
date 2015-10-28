<?php
/**
 * @link https://github.com/yxdj
 * @copyright Copyright (c) 2014 xuyuan All rights reserved.
 * @author xuyuan <1184411413@qq.com>
 */

namespace yxdj\network;


/**
 * api接口是位于客户端与服务端的中间层
 * 用于对客户端请求参数的调整与封装，对服务端响应结果的检查与处理，生成处理结果
 * 最后将处理结果回复给客户端
 * 
 * 请求参数的处理：
 * 不管是什么参数，都不应直接抛异常，总能得到一个响应，哪怕是空的
 * 所以请求参数尽可能直观，能检查更好，不检查的话，在响应处理中做回复
 *
 * 响应结果的处理：
 * content才是结果，code只是辅助信息，它是200不一定内容完好，不是200不一定内容不需要
 * content结果常用3种，text,xml,json
 * 请求的发送是主动的，想怎么发，发什么，发到哪都是自已确定，只要发了都视为发出去
 * 响应的接收是被动的，回复过来的不一定是合法的，但总是有个内容，空也算
 *
 * 所以主要的过程还就是在内容的解析处理上，要的是内容，就检查内容，和code没直接关系
 * 判定响应是否合法，首先要看期待什么样的响应，有哪些明显特征
 * 这和请求的解析类似，看准允什么样的请求，
 * 协议层面的底层自行处理了，要做的就是应用层
 * 
 * 和请求不一样的是，请求的参数形式较通用get,post,cookie，可以逐个分析，另外谁都可以访问
 * 响应就一块content,可存在的形式即text,json,xml，其它
 * text: 比对性的内容直接比较即可，数据性的内容基本不需再做处理，有例外别说
 * json: 转成数组，有明显用于判断的标示名值对，比较即可，其它的也很少处理
 * xml: 和json差不多，
 *
 * 结论，即然可用于请求或响应，两都之间肯定有事先达成一致，总有一个可用于判定的标识
 * 其间的比对过程：
 * 1.请求的发送与响应，这就是一个认证过程
 * 2.把content当成某个格式来处理
 * 3.格式化后验证标识
 *
 * 错误的交互总是可能存在，或伪请求，或伪响应
 * 要做到尽可能的达成信任，必需比对信息明确，清淅，但对第3方又是隐蔽的
 * 所以这个过程是双方的，当单方要达到这个目标的，必需要足够的了解对方的规则
 * 所以规则是应该双方协商的，当一方已经确定，另一方就要努力发现已确定的规则
 * 
 * 任何交互都是如此，ajax也是
 * 要交互之前首先就确定或发现规则
 *
 * 常用的交互规则：
 * text:
 * 1.单一状态；
 * 2.单一内容，按指定规则解析出状态和内容；
 * 3.按一定规则分解出状态或内容,json/xml就是两种现成的格式
 * 
 * json:
 * {
 * "status":"ok",//状态
 * "cout":100,//其它辅助信息
 * "data":[]//内空
 * }
 *
 * 检查status是否是存在并按特定方式查收内容，这就是最好的方式了，
 * 还要更好，那就是从安全性和隐蔽性上处理了
 *
 * 另外需要重发请求的情况是：由于意外或随机产生的
 */
class Api
{
    //协议
    public static $protocol=array();


    public static function __callstatic($action,$params){
        $action = get_called_class().':'.$action;
        return "can't ask a not exists method '{$action}'";
	}
 
    
    //获取或设置http连接
    public static function getHttp($httpConfig=array())
    {
        if (!isset(Api::$protocol['http']) || Api::$protocol['http'] === null) {
            Api::$protocol['http'] = new Http($httpConfig);
        } else {
            Api::$protocol['http']->setConfig($httpConfig);
        } 
        return Api::$protocol['http'];
    }
	
    //获取或设置http连接
    public static function getCurl($httpConfig=array())
    {
        if (!isset(Api::$protocol['curl']) || Api::$protocol['curl'] === null) {
            Api::$protocol['curl'] = new Curl($httpConfig);
        } else {
            Api::$protocol['curl']->setConfig($httpConfig);
        } 
        return Api::$protocol['curl'];
    }

    //获取或设置http连接
    public static function getStream($httpConfig=array())
    {
        if (!isset(Api::$protocol['stream']) || Api::$protocol['stream'] === null) {
            Api::$protocol['stream'] = new Stream($httpConfig);
        } else {
            Api::$protocol['stream']->setConfig($httpConfig);
        } 
        return Api::$protocol['stream'];
    }
    
    //当前api类名
    public static function className()
    {
        return get_called_class();
    }  
}
