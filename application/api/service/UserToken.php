<?php


namespace app\api\service;


use app\lib\enum\ScopeEnum;
use app\lib\exception\WeChatException;
use app\lib\exception\TokenException;
use think\Exception;
use think\facade\Config;
use app\api\model\User as UserModel;

class UserToken extends Token
{
    protected $code;
    protected $wxAppID;
    protected $wxAppSecret;
    protected $wxLoginUrl;

    function __construct($code)
    {
        $this->code = $code;
        $this->wxAppID = Config::get('wx.app_id');
        $this->wxAppSecret = Config::get('wx.app_secret');
        $this->wxLoginUrl = sprintf(Config::get('wx.login_url'),
            $this->wxAppID,$this->wxAppSecret,$this->code);
    }

    public function get(){
        $result = curl_get($this->wxLoginUrl);
        $wxResult = json_decode($result,true);
        if(empty($wxResult)){
            throw new Exception('微信内部错误');
        }else{
            $loginFail = array_key_exists('errorcode',$wxResult);

            if($loginFail){
                $this->processLoginError($wxResult);
            }else{
                $token = $this->grantToken($wxResult);
                return $token;
            }
        }
    }

    // 返回成功
    private function grantToken($wxResult)
    {
        // 拿到openid
        // 去数据库查看该openid是不是已经存在
        // 存在则不处理，不存在则新增一条user记录
        // 生成令牌，准备缓存数据，写入缓存
        // 返回令牌
        // key: token value； wxResult , uid , scope
//        var_dump($wxResult);die();
        $openid = $wxResult["openid"];
        $user = UserModel::getByOpenID($openid);
        if($user){
            $uid = $user->id;
        }else{
            // new user
            $uid = $this->newUser($openid);
        }
        $cachedValue = $this->prepareCachedValue($wxResult,$uid);
//        var_dump($cachedValue);
        $token = $this->saveCache($cachedValue);
//        var_dump($token);die();
        return $token;
    }

    private function prepareCachedValue($wxResult,$uid){
        $cachedValue = $wxResult;
        $cachedValue['uid'] = $uid;
        // scope = 16 代表app用户权限数值 ， scope = 32 代表cms用户权限数值
        $cachedValue['scope'] = ScopeEnum::User;
        return $cachedValue;
    }
    private function saveCache($cachedValue){
        $key = self::generateToken();
        $value = json_encode($cachedValue);
        $expire_in = Config::get('setting.token_expire_in');

        $request = cache($key,$value,$expire_in);
        if(!$request){
            throw new TokenException([
                'msg'=>'服务器缓存异常',
                'errorCode'=>'10005'
            ]);
        }
        return $key;
    }

    // 处理返回错误
    private function processLoginError($wxResult){
        throw new WeChatException([
            'msg' => $wxResult['errmsg'],
            'errorCode' => $wxResult['errcode'],
        ]);
    }

    // 添加新用户
    private function newUser($openid)
    {
        // 有可能会有异常，如果没有特别处理
        // 这里不需要try——catch
        // 全局异常处理会记录日志
        // 并且这样的异常属于服务器异常
        // 也不应该定义BaseException返回到客户端
        $user = UserModel::create(
            [
                'openid' => $openid
            ]);
        return $user->id;
    }
}