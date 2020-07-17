<?php


namespace app\api\Validate;


class Count extends BaseValidate
{
    protected $rule=[
        'count' => 'isPositiveInt|between:1,15'
    ];
}