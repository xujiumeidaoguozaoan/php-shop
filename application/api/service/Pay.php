<?php


namespace app\api\service;


use app\api\service\Token as TokenService;
use app\lib\enum\OrderStatusEnum;
use app\lib\exception\OrderException;
use app\lib\exception\TonkenException;
use think\Exception;
use app\api\model\Order as OrderModel;
use app\api\service\Order as OrderService;
use think\facade\Config;
use think\facade\Log;

require "../extend/WxPay/WxPay.Api.php";


class Pay
{
    private $orderID;
    private $orderNO;
    function __construct($orderID)
    {
        if(!$orderID){
            throw new Exception('订单号不允许为空');
        }
        $this->orderID = $orderID;
    }
    public function pay()
    {
        // 订单号可能不存在
        // 订单号存在，但与当前用户不匹配
        // 订单有可能已经被支付
        $this->checkOrderValid();
        // 进行库存量检测
        $orderService = new OrderService();
        $status = $orderService->checkOrderStock($this->orderID);
        if(!$status['pass']){
            return $status;
        }
        return $this->makeWxPreOrder($status['orderPrice']);
    }

    // 微信预订单
    private function makeWxPreOrder($totalPrice)
    {
        $openid = TokenService::getCurrentTokenVar('openid');
        if(!$openid){
            throw new TonkenException();
        }
        $wxOrderData = new \WxPayUnifiedOrder();
        $wxOrderData->SetOut_trade_no($this->orderNO);
        $wxOrderData->SetTrade_type('JSAPI');
        $wxOrderData->SetTotal_fee($totalPrice);
        $wxOrderData->SetBody('零食');
        $wxOrderData->SetOpenid($openid);
        // 填写支付回调地址
        $wxOrderData->SetNotify_url(Config::get('secure.pay_back_url'));
        return $this->getPaySignature($wxOrderData);
    }

    // 获得支付签名
    private function getPaySignature($wxOrderData)
    {
        // 此步需配置wxpay.config中的appid和商户号等，目前除setnotify_url外，都还success
        $wxOrder = \WxPayApi::unifiedOrder($wxOrderData);
        if($wxOrder['return_code'] != 'SUCCESS'||
            $wxOrderData['result_code'] != 'SUCCESS')
        {
            Log::record($wxOrder,'error');
            Log::record('获取预支付订单失败','error');
        }

        // 处理prepay_id
        $this->recordPreOrder($wxOrder);
        $signature = $this->sign($wxOrder);
        return $signature;
    }

    // 获得需要返回给小程序的数据
    private  function sign($wxOrder)
    {
        $jsApiPayData = new \WxPayJsApiPay();
        $jsApiPayData->SetAppid(Config::get('wx.app_id'));
        $jsApiPayData->SetTimeStamp((string)time());
        $rand = md5(time() . mt_rand(0,1000));
        $jsApiPayData->SetNonceStr($rand);
        $jsApiPayData->SetPackage('prepay_id='.$wxOrder['prepay_id']);
        $jsApiPayData->SetSignType('md5');

        $sign = $jsApiPayData->MakeSign();
        $rawValues = $jsApiPayData->GetValues();
        $rawValues['paySign'] = $sign;

        unset($rawValues['appId']);

        return $rawValues;
    }
    // 记录prepay_id
    private function recordPreOrder($wxOrder)
    {
        OrderModel::where('id',$this->orderID)
            ->update(['prepay_id'=>$wxOrder['prepay_id']]);
    }

    // 检测订单合法性
    private function checkOrderValid()
    {
        $order = OrderModel::where('id',$this->orderID)->find();
        if(!$order){
            throw new OrderException();
        }
        if(!TokenService::isValidOperate($order->user_id)){
            throw new TonkenException([
                'msg' => '订单与用户不匹配',
                'errorCode' => 10003
            ]);
        }
        // status=1代表待支付
        if($order->status != OrderStatusEnum::UNPAID){
            throw new OrderException([
                'msg' => '订单已支付',
                'errorCode' => 80003,
                'code' => 400
            ]);
        }
        $this->orderNO = $order->order_no;
        return true;
    }
}