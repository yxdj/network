<?php
/**
 * @link https://github.com/yxdj
 * @copyright Copyright (c) 2014 xuyuan All rights reserved.
 * @author xuyuan <1184411413@qq.com>
 */

namespace yxdj\network\api;

use yxdj\network\Api;

class TestApi extends Api
{
    /**
     * test
     */
    public static function test($data)
    {
        return print_r($data, true);
    }
    
    /**
     * code
     */    
    public static function code($data)
    {
        try {
            return Api::getHttp()->request($data)->getCode();
        } catch (\Exception $e) {
            return '';
        }      
    }

    /**
     * debug
     */
    public static function debug($data)
    {
        try {
            return Api::getHttp()->request($data)->getDebug();
        } catch (\Exception $e) {
            return '';
        }        
    }
    
    /**
     * keyword
     */
    public static function keyword($data)
    {
        try {
            return Api::getHttp()->request($data)->getKeyword();
        } catch (\Exception $e) {
            return '';
        }        
    }
    
    /**
     * charset
     */
    public static function charset($data)
    {
        try {
            return Api::getHttp()->request($data)->getCharset();
        } catch (\Exception $e) {
            return '';
        }  
    }
    
    /**
     * content,text/xml/json
     */
    public static function content($data)
    {
        try {
            return Api::getHttp()->request($data)->getContent();
        } catch (\Exception $e) {
            return '';
        }
    }

    public static function text($data)
    {

        $content = Api::getHttp()->request($data)->getContent();
        return $content;


    }

	
    public static function json($data)
    {

        $content = Api::getHttp()->request($data)->getContent();
        return json_decode($content, true);
    }
	
    
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