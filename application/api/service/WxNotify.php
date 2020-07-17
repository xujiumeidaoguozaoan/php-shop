<?php


namespace app\api\service;

use app\api\model\Order as OrderModel;
use app\api\model\Product;
use app\api\service\Order as OrderService;
use app\lib\enum\OrderStatusEnum;
use think\Db;
use think\facade\Log;

require '../extend/WxPay/WxPay.Api.php';

class WxNotify extends \WxPayNotify
{
//<xml>
//<appid><![CDATA[wx2421b1c4370ec43b]]></appid>
//<attach><![CDATA[支付测试]]></attach>
//<bank_type><![CDATA[CFT]]></bank_type>
//<fee_type><![CDATA[CNY]]></fee_type>
//<is_subscribe><![CDATA[Y]]></is_subscribe>
//<mch_id><![CDATA[10000100]]></mch_id>
//<nonce_str><![CDATA[5d2b6c2a8db53831f7eda20af46e531c]]></nonce_str>
//<openid><![CDATA[oUpF8uMEb4qRXf22hE3X68TekukE]]></openid>
//<out_trade_no><![CDATA[1409811653]]></out_trade_no>
//<result_code><![CDATA[SUCCESS]]></result_code>
//<return_code><![CDATA[SUCCESS]]></return_code>
//<sign><![CDATA[B552ED6B279343CB493C5DD0D78AB241]]></sign>
//<time_end><![CDATA[20140903131540]]></time_end>
//<total_fee>1</total_fee>
//<coupon_fee><![CDATA[10]]></coupon_fee>
//<coupon_count><![CDATA[1]]></coupon_count>
//<coupon_type><![CDATA[CASH]]></coupon_type>
//<coupon_id><![CDATA[10000]]></coupon_id>
//<coupon_fee><![CDATA[100]]></coupon_fee>
//<trade_type><![CDATA[JSAPI]]></trade_type>
//<transaction_id><![CDATA[1004400740201409030005092168]]></transaction_id>
//</xml>
    public function NotifyProcess($objData, $config, &$msg)
    {
        if($objData['resulte_code'] == 'SUCCESS'){
            $orderNo = $objData['out_trade_no'];
            Db::startTrans();
            try{
                $order = OrderModel::where('order_no',$orderNo)
                    ->lock(true)
                    ->find();
                if($order->status == 1){
                    $service = new OrderService();
                    $stockStatus = $service->checkOrderStock($orderNo);
                    if($stockStatus['pass']){
                        $this->updateOrderStatus($order->id,true);
                        $this->reduceStock($stockStatus);
                    }
                    else{
                        // 已支付但无货
                        $this->updateOrderStatus($order->id,false);
                    }
                }
                Db::commit();
                return true;
            }catch(Exception $ex){
                Db::rollback();
                Log::record($ex);
                return false;
            }
        }
        else{
            // 已经知晓了微信支付失败
            return true;
        }
    }
    private function reduceStock($stockStatus)
    {
        foreach ($stockStatus['pStatusArray'] as $singlePStatus )
        {
            Product::where('id',$singlePStatus['id'])
                ->setDec('stock',$singlePStatus['count']);
        }
    }
    // 更新订单状态
    private function updateOrderStatus($orderID,$success)
    {
        $status = $success ? OrderStatusEnum::PAID : OrderStatusEnum::PAID_BUT_OUT_OF;
        OrderModel::where('id',$orderID)->update(['status'=>$status]);
    }
}