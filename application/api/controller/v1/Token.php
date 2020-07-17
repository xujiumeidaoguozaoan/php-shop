<?php


namespace app\api\controller\v1;


use app\api\service\UserToken;
use app\api\Validate\TokenGet;

class Token
{

    public function getToken($code = "")
    {
        $validate = new TokenGet();
        $validate->goCheck();

        $ut = new UserToken($code);
        $token = $ut->get();
        return [
            'token'=>$token
        ];
    }
}