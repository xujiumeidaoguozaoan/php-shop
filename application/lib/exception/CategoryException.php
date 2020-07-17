<?php


namespace app\lib\exception;


class CategoryException extends BaseException
{
    public $code = 404;
    public $msg = '类别获取失败';
    public $errorCode = 10001;
}