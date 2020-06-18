<?php
namespace app\index\controller;

use app\index\model\Message as MessageModel;
use app\index\model\Label as LabelModel;
use app\index\model\Article as ArticleModel;
use app\index\model\Link as LinkModel;
use app\index\controller\Common;
use think\App;
use think\Request;


class Index extends Common
{
    public function __construct(Request $request, App $app)
    {
        parent::__construct($request, $app);
    }

    public function index()
    {
        $lst = ArticleModel::get_article();
        $this->assign('lst',$lst);
        return view();
    }
    public function label()
    {
        $lst = LabelModel::lst_labels();
        $this->assign('label',$lst);
        return view();
    }
    public function about()
    {
        return view();
    }
    public function message()
    {
        return view();
    }
    public function message_add()
    {
        if(request()->isPost()){
            $message = $_POST['message'];
            $conn = new MessageModel;
            $data = array(
                'message'=>$message,
                'add_time'=>date('Ymd',intval(time())),
            );
            $conn->save($data);
            return json(['code'=>200,'msg'=>'留言成功']);
        }
    }
    public function link()
    {
        $link = LinkModel::where('show','=',1)->select();
//        $link = LinkModel::all();
//        var_dump($link);die();
        $this->assign('link',$link);
        return view();
    }
    public function grid()
    {
        $lst = ArticleModel::get_article();
        $this->assign('lst',$lst);
        return view();
    }
}
