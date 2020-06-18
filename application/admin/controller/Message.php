<?php
namespace app\admin\controller;

use \think\Controller;
use app\admin\model\Message as MessageModel;
class Message extends Controller{
    public function lst()
    {
        return view();
    }
    public function message_lst()
    {
        $data = MessageModel::all();
        $res = array(
            'code'=>0,
            'msg'=>'',
            'count'=>count($data),
            'data'=>$data,
        );
        return json($res);
    }
}