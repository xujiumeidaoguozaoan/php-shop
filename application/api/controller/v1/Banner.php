<?php


namespace app\api\controller\v1;


use app\api\Validate\IDMustBePositiveInt;
use app\lib\exception\MissException;
use think\Controller;
use app\api\model\Banner as BannerModel;
use think\facade\Config;



class Banner extends Controller
{
    public function getBanner($id)
    {
        (new IDMustBePositiveInt()) -> goCheck();

        $banner =  BannerModel::getBannerByID($id);
        if(!$banner){
            throw new MissException();
        }
        return json($banner);
    }
}