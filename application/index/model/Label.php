<?php
namespace app\index\model;
use think\Model;
use app\index\model\Article as ArticleModel;
class Label extends Model{
    protected static function init()
    {

    }
    public static function lst($label)
    {
        $label = Label::where('label',$label)->find();
        $id = $label['id'];
        $lst = ArticleModel::where('label','like',['%,'.$id,'%,'.$id.',%',$id.',%',$id],'OR')
            ->select();
        return $lst;
    }
    public static function lst_labels()
    {
        $lst = Label::all();
        $labels=array();
        foreach( $lst as $vo )
        {
            $id = $vo['id'];
            $number = ArticleModel::where('label','like',['%,'.$id,'%,'.$id.',%',$id.',%',$id],'OR')->count();
            $labels[] = ['label'=>$vo['label'],'number'=>$number];
        }
        return $labels;
    }
}