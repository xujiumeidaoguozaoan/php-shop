<?php


namespace app\api\model;


class User extends BaseModel
{
    protected $hidden = ['update_time','delete_time'];
    public function address(){
        return $this->hasOne('UserAddress','user_id','id');
    }

    public static function getByOpenID($openid){
        $user = self::where('openid',$openid)->find();
        return $user;
    }

}