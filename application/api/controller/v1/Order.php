<?php


namespace app\api\controller\v1;


use app\api\controller\BaseController;
use app\api\Validate\IDMustBePositiveInt;
use app\api\Validate\OrderPlace;
use app\api\service\Token as TokenService;
use app\api\service\Order as OrderService;
use app\api\Validate\PagingParameter;
use app\api\model\Order as OrderModel;
use app\lib\exception\OrderException;

class Order extends BaseController
{
    // 用户选择商品后，向api提交包含他所选商品的信息
    // api接收到信息后，需要检查订单相关商品的库存
    // 有库存，将订单数据存入数据库
    // 下单成功，告诉客户端，可以进行支付
    // 调用支付API
    // 再次进行库存量检测
    // 服务器调用微信支付接口，进行支付
    // 小程序根据服务器返回结果，拉起微信支付
    // 微信会返回支付结果（异步）
    // 成功：也需要检测库存量
    // 成功：扣除库存量 失败：返回失败的结果

    protected $beforeActionList = [
        'checkExclusiveScope'=>['only'=>'placeOrder'],
        'checkPrimaryScope' => ['only'=>'getDetail,getSummaryByUser']
    ];

    // 获取历史订单
    public function getSummaryByUser($page=1,$size=15)
    {
        (new PagingParameter())->goCheck();
        $uid = TokenService::getCurrentUid();
        $pagingOrder = OrderModel::getSummaryByUser($uid,$page,$size);
        if($pagingOrder->isEmpty()){
            return [
                'data' => '',
                'current_page' => $pagingOrder->getCurrentPage(),
            ];
        }
        $data = $pagingOrder->hidden(['prepay_id','snap_items','snap_address'])->toArray();
        return [
            'data' => $data,
            'current_page' => $pagingOrder->getCurrentPage(),
        ];
    }

    // 订单详情
    public function getDetail($id)
    {
        (new IDMustBePositiveInt()) -> goCheck();
        $orderDetail = OrderModel::get($id);
        if(!$orderDetail){
            throw new OrderException();
        }
        return $orderDetail->hidden(['prepay_id']);
    }

    // 生成订单
    public function placeOrder()
    {
        (new OrderPlace())->goCheck();
        $products = input('post.products/a');
        $uid = TokenService::getCurrentUid();

        $order = new OrderService();
        $status = $order->place($uid,$products);
        return $status;
    }
}