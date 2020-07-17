<?php


namespace app\api\Validate;


use think\Validate;
use app\lib\exception\ParameterException;
use app\lib\exception\BaseException;
use think\facade\Request;

class BaseValidate extends Validate
{
    public function goCheck()
    {
        //必须设置contetn-type:application/json
//        $params['token'] = $request->header('token');
        $params = Request::param();
        if (!$this->batch()->check($params)) {
            $exception = new ParameterException(
                [
                    'msg' =>  $this->error,
                ]);
            throw $exception;
        }
        return true;
    }
    // 验证正整数
    protected function isPositiveInt($value,$rule='',$data='',$field='')
    {
        if (is_numeric($value) && is_int($value + 0) && ($value + 0) > 0) {
            return true;
        }
        return false;
    }
    // 验证是否为空
    protected function isNotEmpty($value, $rule='', $data='', $field='')
    {
        if (empty($value)) {
            return $field . '不允许为空';
        } else {
            return true;
        }
    }

    //没有使用TP的正则验证，集中在一处方便以后修改
    //不推荐使用正则，因为复用性太差
    //手机号的验证规则
    protected function isMobile($value)
    {
        $rule = '^1(3|4|5|7|8)[0-9]\d{8}$^';
        $result = preg_match($rule, $value);
        if ($result) {
            return true;
        } else {
            return false;
        }
    }

    // 过滤参数
    public function getDataByRule($arrays)
    {
        if (array_key_exists('user_id', $arrays) | array_key_exists('uid', $arrays)) {
            // 不允许包含user_id或者uid，防止恶意覆盖user_id外键
            throw new ParameterException([
                'msg' => '参数中包含有非法的参数名user_id或者uid'
            ]);
        }
        $newArray = [];
        foreach ($this->rule as $key => $value) {
            $newArray[$key] = $arrays[$key];
        }
        return $newArray;
    }
}