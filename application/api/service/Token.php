<?php


namespace app\api\service;



use app\lib\enum\ScopeEnum;
use app\lib\exception\ForbiddenException;
use app\lib\exception\TonkenException;
use think\facade\Cache;
use \think\facade\Request;
use think\facade\Config;
use \think\Exception;

class Token
{
    public static function generateToken(){
        // 32个字符组成一个字符串
        $randChars = getRandChar(32);
        $timestamp = $_SERVER['REQUEST_TIME_FLOAT'];
        $tokenSalt = Config::get('secure.token_salt');
        return md5($randChars . $timestamp . $tokenSalt);
    }
    public static function getCurrentTokenVar($key)
    {
        $token = Request::header('token');
        $var = Cache::get($token);
        if(!$var) throw new TonkenException();
        else{
            if(!is_array($var))  $var = json_decode($var,true);
            if(array_key_exists($key,$var)) return $var[$key];
            else throw new Exception('尝试获取的token变量并不存在');
        }
    }
    public static function getCurrentUid()
    {
        //token
        $uid = self::getCurrentTokenVar('uid');
        return $uid;
    }

    // 用户和cms管理员都可以访问的权限
    public static function needPrimaryScope()
    {
        $scope =  self::getCurrentTokenVar('scope');
        if($scope){
            if($scope >= ScopeEnum::User) return true;
            else throw new ForbiddenException();
        }else{
            throw new TonkenException();
        }
    }
    // 用户可访问的权限
    public static function needExclusiveScope()
    {
        $scope =  self::getCurrentTokenVar('scope');
        if($scope){
            if($scope == ScopeEnum::User) return true;
            else throw new ForbiddenException();
        }else{
            throw new TonkenException();
        }
    }

    public static function isValidOperate($checkedUID)
    {
        if(!$checkedUID){
            throw new Exception('检测UID时必须传入一个被检查的UID');
        }
        $currentOperateUID = self::getCurrentUid();
        if($currentOperateUID == $checkedUID) return true;
        else return false;
    }
}