<?php
/**
 * @link https://github.com/yxdj
 * @copyright Copyright (c) 2014 xuyuan All rights reserved.
 * @author xuyuan <1184411413@qq.com>
 */

namespace yxdj\network\api;

use yxdj\network\Api;
use Yii;
use common\Models\User;

class UserApi extends Api
{
    /**
     * login
     * 执行的过程中绝不能产生语法异常，也不能有不可预知的异常抛出
     * 
     * 值得重试的可选项：连接超时，读取超时，其它合理的错误返回
     * 这些都是有一定随机性的。以下两种情况不要重试了：
     * 1.不强制一定有正确结果返回
     * 2.重试成功的可能性小或没有
     * 
     * 重试：
     * 1.意外：超时，断线
     * 2.随机返回
     */
    public static function login2($data=[])
    {
        $i = 50;
    
        while($i>0){
            $content = Api::getHttp()->get('http://yii.app.com/login.html', $data)
            ->getContent();
            
            //返回成功
            if ($content == 'ok') {
                return 'ok'; 
                
            //可以重试，有随机性            
            } elseif ($content == 'ng2') {
                $i--;
                continue;
            
            //不必试了
            } else {
                return null;
            }
        }

        return null;

    }    

    
    public static function login($data=[])
    {
        //验证，取值
        if (!isset($data['username'], $data['password'])) {
            return false;
        }
        $username = $data['username'];
        $password = $data['password'];
        $rememberMe = empty($data['rememberMe']) ? 0 : 3600 * 24 * 30;
        
        //找出用户
        $user= User::findByUsername($username);
        
        //比对密码，如果登录成功将重置会话
        if ($user && $user->validatePassword($password)) {
            return Yii::$app->user->login($user, $rememberMe);
        } else {
            return false;
        }
    }
    
    public static function logout()
    {
        return Yii::$app->user->logout();
    }
    
    public function validatePassword($attribute, $params)
    {
		//验证尚未出现错误才验证，已经有错了就不必了
        if (!$this->hasErrors()) {
			//刚刚取得的用户信息,xxx,不是刚刚，这里先取的，
            $user = $this->getUser();
			//用户不存在，或验证不成功,这里的user实现于接口
			//这里传的密码是用户输的密码
            if (!$user || !$user->validatePassword($this->password)) {
                $this->addError($attribute, 'Incorrect username or password.');
            }
        }
    }

    /**
     * Logs in a user using the provided username and password.
     *
     * @return boolean whether the user is logged in successfully
     */
    public function login_test()
    {
		//验证，包括对密码的验证，
		//如果走到对密码验证这一步，用户信息已经取出来了
        if ($this->validate()) {
            return Yii::$app->user->login($this->getUser(), $this->rememberMe ? 3600 * 24 * 30 : 0);
        } else {
            return false;
        }
    }

    /**
     * Finds user by [[username]]
     *
     * @return User|null
     */
	//通过用户名把用户信息取出来,
	//这里不管密码对不，用户匹配上就取出来了,这里返回false可能
    public function getUser()
    {
        if ($this->_user === false) {
            $this->_user = User::findByUsername($this->username);
        }
        return $this->_user;
    }    
    
}
