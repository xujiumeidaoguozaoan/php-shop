<?php
namespace app\index\controller;

use app\index\model\Visitor as VisitorModel;
use think\App;
use \think\Controller;
use \think\facade\Cache;
use think\model\concern\TimeStamp;
use think\Request;

class Common extends Controller{
    public function __construct(Request $request, App $app)
    {
        parent::__construct($request, $app);
        static::record_pv();
    }

    // 获得redis实例
    public static function get_redis()
    {
        if(isset($redis)) return $redis;
        $res = Cache::init();
        $redis = $res->handler();
        return $redis;
    }
    // 记录pv
    public static function record_pv(){
        $ip = static::get_addr();
        if($ip){
            $user = VisitorModel::where('ip_addr',$ip)->find();
            if($user){
                $user->times++;
            }else{
                $user = new VisitorModel;
                $user->ip_addr = $ip;
                $user->times = 1;
            }
            // php的timestamp转为mysql的timestamp
            $user->last_time = date('Y-m-d H:i:s',time());
            // mysql的timestamp转为php的timestamp
//            $phptime=strtotime($mysqldate);
            $user->save();
        }
    }

    // 获得ipaddr
    public static function get_addr()
    {
        $ip = false;
        if(!empty($_SERVER['REMOTE_ADDR'])){
            $ip = $_SERVER['REMOTE_ADDR'];
        }
        return $ip;
    }
}
