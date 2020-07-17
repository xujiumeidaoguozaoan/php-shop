<?php


namespace app\lib\exception;


class ProductException extends \Exception
{
    public $code = 404;
    public $msg = '请检查参数';
    public $errorCode = 30000;
}