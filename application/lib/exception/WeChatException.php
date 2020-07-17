<?php


namespace app\lib\exception;


class WeChatException extends BaseException
{
    public $code = 400;
    public $msg = '微信接口调用失败';
    public $errorCode = 30000;
}