<?php


namespace app\api\Validate;



class IDMustBePositiveInt extends BaseValidate
{
    protected $rule = [
        'id' => 'require|isPositiveInt',
        'num' => 'in:1,2,3'
    ];
    protected $message = [
        'id' => 'id必须是正整数',
    ];
}