<?php
namespace app\admin\controller;
use \think\Controller;
use app\admin\model\Admin as AdminModel;
class Index extends Controller{
    public function index()
    {
        if(!session('name')){
            $this->redirect('admin/index/login');
        }
        return view();
    }
    public function login()
    {
        if(request()->isPost()){
            $username = $_POST['username'];
            $pass = md5($_POST['password']);
            $conn = AdminModel::get(1);
            if($username==$conn['username'] & $pass==$conn['password']){
                session('name',$conn['nickname']);
                $this->redirect('admin/index/index');
            }
        } else{
            session(null);
            return view();
        }
    }
}