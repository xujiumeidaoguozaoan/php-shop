<?php


namespace app\api\Validate;


use app\lib\exception\BaseException;
use app\lib\exception\ParameterException;

class OrderPlace extends BaseValidate
{
    protected $rule = [
        'products' => 'checkProducts'
    ];
    protected $singleRule = [
        'product_id' => 'require|isPositiveInt',
        'count' => 'require|isPositiveInt'
    ];
    protected function checkProducts($values)
    {
        if(!is_array($values)){
//            echo "this";
            throw new ParameterException([
                'msg' => '商品参数错误'
            ]);
        }
        if(empty($values)){
            throw new ParameterException([
                'msg' => '商品列表不能为空'
            ]);
        }
        foreach ($values as $value){
            $this->checkProduct($value);
        }
        return true;
    }
    protected function checkProduct($value)
    {
        $validate = new BaseValidate($this->singleRule);
        $result = $validate->check($value);
//        var_dump($value);
        if(!$result){
            throw new ParameterException([
                'msg' => '商品参数错误'
            ]);
        }
        return $result;
    }

}