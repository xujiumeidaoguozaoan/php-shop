<?php


namespace app\api\controller\v1;


use app\api\controller\BaseController;
use app\api\service\WxNotify;
use app\api\Validate\IDMustBePositiveInt;
use app\api\service\Pay as PayService;

class Pay extends BaseController
{
    protected $beforeActionList = [
        'checkExclusiveScope'=>['only'=>'getPreOrder']
    ];

    public function getPreOrder($id='')
    {
        (new IDMustBePositiveInt())->goCheck();
        $pay = new PayService($id);
        return $pay->pay();
    }

    // 接受微信返回信息
    public function receiveNotify()
    {
        // 1.检查库存量，超卖
        // 2.更新订单状态status
        // 3.更新库存
        // 成功处理，返回微信成功处理的消息，否则，告诉微信没有成功处理

        // 特点：post；xml格式；不会携带？型参数
        $nofity = new WxNotify();
        $nofity->Handle();
    }
}