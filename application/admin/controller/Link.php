<?php
namespace app\admin\controller;

use \think\Controller;
use app\admin\model\Link as LinkModel;
class Link extends Controller{
    public function lst()
    {
        return view();
    }
    public function link_lst()
    {
        $data = LinkModel::all();
        $res = array(
            'code' => 0,
            'msg' => '',
            'count' => count($data),
            'data' => $data
        );
        return json($res);
    }
    public function add()
    {
        return view();
    }
    public function link_add()
    {
        if(request()->isPost()){
            $name = $_POST['name'];
            $link = $_POST['link'];
            $show = 'show';
            if(isset($_POST[$show])) $show = $_POST['show'];
            else $show = 0;
            $data=array(
                'name'=>$name,
                'link'=>$link,
                'show'=>$show
            );
            $conn = new LinkModel();
            $res = [];
            if($conn->save($data)){
                $res['status'] = 200;
                $res['msg'] = '添加链接成功';
                }
            }else{
                $res['status'] = 500;
                $res['msg'] = '添加链接失败';
            }
        return json($res);
    }
    public function link_edit()
    {
        if(request()->isGet()){
            $id = $_GET['id'];
            $link = LinkModel::get($id);
            $this->assign('link',$link);
            return view();
        }
    }
    public function edit_action()
    {
        if(request()->isPost()){
            $id = $_POST['id'];
            $link_data = LinkModel::get($id);
            $name = $_POST['name'];
            $data = [];
            $link = $_POST['link'];
            $show = 'show';
            if(isset($_POST[$show])) $data['show'] = $_POST['show'];
            else $data['show'] = 0;
            if($name<>$link_data['name']) $data['name'] = $name;
            if($link<>$link_data['link']) $data['link'] = $link;
            $res = [];
            if(count($data)>0){
                $conn = LinkModel::get($id);
                if($conn->save($data)){
                    $res['code'] = 200;
                    $res['msg'] = '更新完成';
                }else{
                    $res['code'] = 500;
                    $res['msg'] = '更新失败';
                }
            }else{
                $res['code'] = 500;
                $res['msg'] = '无更新需要';
            }
            return json($res);
        }
    }
    public function link_del()
    {
        if(request()->isPost()){
            $id = $_POST['id'];
            LinkModel::destroy($id);
        }
    }
}