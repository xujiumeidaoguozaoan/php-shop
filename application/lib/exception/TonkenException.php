<?php


namespace app\lib\exception;


class TonkenException extends BaseException
{
    public $code = 401;
    public $msg = 'token已过期或token获取失败';
    public $errorCode = 10001;
}