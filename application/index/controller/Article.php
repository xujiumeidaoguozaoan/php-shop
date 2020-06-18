<?php
namespace app\index\controller;

use app\index\model\Article as ArticleModel;
use app\index\controller\Common;
class Article extends Common
{
    public function read($id)
    {
        $redis = Common::get_redis();
        // 从redis中拿到数据
        $article =  $redis->get('article_'.$id);
        $article = json_decode($article,true);
//        var_dump($article);die();
        // redis中的点击次数加一
        $article['click_times'] ++;
        $data = $article;
        $data['content'] = html_entity_decode($data['content']);
        $this->assign('article',$data);
        // 再次存回redis中
        $article = json_encode($article);
        $redis->set('article_'.$id,$article);
        //数据库中点击次数加一
        ArticleModel::click_once($id);
        return view();
    }
}