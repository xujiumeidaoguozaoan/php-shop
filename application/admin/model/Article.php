<?php
namespace app\admin\model;
use app\admin\model\Label as LabelModel;
use think\Model;
class Article extends Model{
//    protected $table = 'article';
    protected static function init()
    {

    }
    public static function get_labels($data)
    {
        if(count($data) > 1){
            foreach($data as &$data_one){
                $ids = explode(',',$data_one['label']);
                $labels = LabelModel::all($ids);
                $label = '';
                for ($i=0;$i<count($labels);$i++)
                {
                    $label= $label.','.$labels[$i]['label'];
                }
                $data_one['label'] = substr($label,1);
            }
        }
        return $data;
    }
    public static function get_label($label)
    {
        $ids = explode(',',$label);
        $labels = LabelModel::all($ids);
        $label = '';
        for ($i=0;$i<count($labels);$i++)
        {
            $label= $label.','.$labels[$i]['label'];
        }
        $label = substr($label,1);
        return $label;
    }
    public static function edit_get($id)
    {
        $data = Article::get($id);
        $data['label'] = explode(',',$data['label']);
        return $data;
    }
    public static function get_all_data()
    {
        $data = Article::all();
        $data = Article::get_labels($data);
        return $data;
    }
}