<?php
namespace app\index\model;
use think\Model;
use app\index\model\Label as LabelModel;
class Article extends Model{
    protected static function init()
    {

    }
    public static function get_article()
    {
        $data = Article::where('status',1)
            ->order('add_time','desc')
            ->limit('0','10')
            ->field(['id','title'])
            ->select();
        return $data;
    }
    public static function click_once($id)
    {
        $res = Article::get($id);
        $res->click_times = $res->click_times+1;
        $res->save();
//        $data = Article::get($id);
//        $ids = explode(',',$data['label']);
//        $labels = LabelModel::all($ids);
//        $label = '';
//        for ($i=0;$i<count($labels);$i++)
//        {
//            $label= $label.','.$labels[$i]['label'];
//        }
//        $data['label'] = substr($label,1);
//        return $data;
    }
}