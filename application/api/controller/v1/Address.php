<?php


namespace app\api\controller\v1;


use app\api\controller\BaseController;
use app\api\Validate\AddressNew;
use \app\api\service\Token as TokenService;
use app\api\model\User as UserModel;
use app\lib\exception\SuccessMessage;
use app\lib\exception\UserException;

class Address extends BaseController
{
    protected $beforeActionList = [
        'checkPrimaryScope'=>['only'=>'createOrUpdateAddress']
    ];

    public function createOrUpdateAddress()
    {
        // 根据token拿到uid
        // 根据uid查找用户数据
        // 获取用户提交的地址信息
        // 根据地址信息，判断是添加还是更新
//        (new AddressNew())->goCheck();
        $validate = new AddressNew();
        $validate -> goCheck();

        $uid = TokenService::getCurrentUid();
        $user = UserModel::get($uid);
        if(!$user) throw new UserException();

        $dataArray = $validate->getDataByRule(input('post.'));

        $userAddress = $user->address;
        if(!$userAddress) {
            // 新增
            $user->address()->save($dataArray);
        }else $user->address->save($dataArray); // 更新
//        return $user;
        return json(new SuccessMessage(),201);
    }
}