<?php
namespace app\admin\controller;
use \think\facade\Cache;
use think\Request;

class Common{
    public static function get_redis()
    {
        if(isset($redis)) return $redis;
        $res = Cache::init();
        $redis = $res->handler();
        return $redis;
    }
    public function upload()
    {
        $file = $_FILES['editormd-image-file'];
//        var_dump($file);die();
        $des_url = './static/upload/';
        $filename = $file['name'];
        $url = $des_url.$filename;
        $up_load  = move_uploaded_file($file['tmp_name'], $url);
        if($up_load){
            $res = array(
                'success'=> 1,
                'message'=> '上传成功',
                'url'=>'http://blog.kaiot.xyz/static/upload/'.$filename
            );
        }
        return json($res);
    }
//{
//success : 0 | 1, //0表示上传失败;1表示上传成功
//message : "提示的信息",
//url     : "图片地址" //上传成功时才返回
//}
}
