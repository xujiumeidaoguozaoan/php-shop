<?php
namespace app\admin\controller;
use \think\Controller;
use app\admin\model\Label as LabelModel;
use app\admin\model\Article as ArticleModel;
use app\admin\controller\Common;
class Article extends Controller{
    public function lst()
    {
        return view();
    }
    public function add()
    {
        $label = LabelModel::all();
        $this->assign('label',$label);
        return view();
    }
    public function article_del()
    {
        if(request()->isPost()){
            $id = $_POST['id'];
            ArticleModel::destroy($id);
            $redis = Common::get_redis();
            $redis->del('article_'.$id);
        }
    }
    public function article_edit()
    {
        if(request()->isGet()){
            $id = $_GET['id'];
            $article = ArticleModel::edit_get($id);
            $labels = LabelModel::all();
            $article['md_content'] = json_decode($article['md_content']);
            $this->assign('article',$article);
            $this->assign('labels',$labels);
            return view();
        }
    }
    public function edit_action()
    {
        if(request()->isPost()) {
            $id = $_POST['id'];
            $article = ArticleModel::edit_get($id);
            $title = $_POST['title'];
            $label = 'label';
            $data = [];
            if (isset($_POST[$label])) {
                $label = $_POST['label'];
                $labels = array_merge($label, $article['label']);
                $label_ = array_intersect($label, $article['label']);
                $label = array_diff($labels, $label_);
                sort($label);
                $data['label'] = implode(',', $label);
            }

            $md_content = $_POST['content'];
            $content = $_POST['md-html-code'];
            // 处理特殊数据
            $md_content = json_encode($md_content);
            $content = htmlentities($content);

            $data['md_content'] = $md_content;
            $data['content'] = $content;

            $status = 'status';
            if (isset($_POST[$status])) $data['status'] = $_POST['status'];
            else $data['status'] = 0;
            if ($title <> $article['title']) $data['title'] = $title;
            $res = [];
            if (count($data) > 0) {
                $conn = ArticleModel::get($id);
                if ($conn->save($data)) {
                    $res['code'] = 200;
                    $res['msg'] = '更新完成';
                    if ($conn->status <> 0) {
                        $id = $conn->id;
                        $redis = Common::get_redis();
                        $conn['label'] = ArticleModel::get_label($conn['label']);
                        $data = json_encode($conn);
                        $redis->set('article_' . $id, $data);
                    } else {
                        $res['code'] = 500;
                        $res['msg'] = '更新失败';
                    }
                } else {
                    $res['code'] = 500;
                    $res['msg'] = '无更新需要';
                }
            }
            return json($res);
        }
    }
    public function article_lst()
    {
        $data = ArticleModel::get_all_data();
        $count = count($data);
        $res['code'] = 0;
        $res['msg'] = '';
        $res['count'] = $count;
        $res['data'] = $data;
        return json($res);
    }

    public function article_add()
    {
        if(request()->isPost()){
            // 读参数
            $title = $_POST['title'];
            $label = $_POST['label'];
            $md_content = $_POST['content'];
            $content = $_POST['md-html-code'];
            // 处理特殊数据
            $md_content = json_encode($md_content);
            $content = htmlentities($content);
            $status = 'status';
            if(isset($_POST[$status])) $status = $_POST['status'];
            else $status = 0;
            $label =  implode(',',$label);
            // 构造字段形式的数据
            $data=array(
                'title'=>$title,
                'label'=>$label,
                'add_time'=>date('Ymd',intval(time())),
                'click_times'=>0,
                'review'=>0,
                'status'=>$status,
                'content'=>$content,
                'md_content'=>$md_content
            );
            $conn = new ArticleModel;
            $res = [];
            if($conn->save($data)){
                $res['status'] = 200;
                $res['msg'] = '添加文章成功';
                if($conn->status<>0){
                    $id = $conn->id;
                    $redis = Common::get_redis();
                    $data['label'] = ArticleModel::get_label($data['label']);
                    $data = json_encode($data);
                    $redis->set('article_'.$id,$data);
                }
            }else{
                $res['status'] = 500;
                $res['msg'] = '添加文章失败';
            }
            return json($res);
        }
    }
    public function label()
    {
        return view();
    }
    public function label_data()
    {
        $result = LabelModel::all();
        if($result){
            $res['code'] = 0;
            $res['msg'] = '';
            $res['count'] = count($result);
            $res['data'] = $result;
            return json($res);
        }
    }
    public function label_add()
    {
        if(request()->isPost()){
            $label = $_POST['label'];
            $res = [];
            $result = LabelModel::where('label',$label)->find();
            if($result){
                $res['status'] = 500;
                $res['msg'] = '该标签已存在';
            }else{
                $conn = new LabelModel;
                $data['label'] = $label;
                $data['add_time'] = date("Ymd",intval(time()));
                if($conn->save($data)){
                    $res['status'] = 200;
                    $res['msg'] = '添加成功';
                }else{
                    $res['status'] = 500;
                    $res['msg'] = '添加失败';
                }
            }
            return json($res);
        }
    }
    public function label_del()
    {
        if(request()->isPost()){
            $id = $_POST['id'];
            $label_id = '%'.$id.'%';
            $conn = new ArticleModel;
            $res = $conn->where('label','like',$label_id)->find();
            $data = [];
            if($res){
                $data['msg'] = '该类别还在使用，删除失败';
            }else{
                $data['status'] = 200;
                LabelModel::destroy($id);
            }
            return json($data);
        }
    }
}