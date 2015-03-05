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

