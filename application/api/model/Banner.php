<?php


namespace app\api\model;


use think\Db;
use think\Exception;
use think\Model;


class Banner extends BaseModel
{
    protected $hidden = ['delete_time','update_time'];
    public function items()
    {
        // 关联模型
        return $this->hasMany('BannerItem','banner_id','id');
    }
    public static function getBannerByID($id)
    {

        $res = self::with(['items','items.img'])
            ->find($id);

        return $res;
    }
}