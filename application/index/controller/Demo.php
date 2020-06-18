<?php
namespace app\index\controller;

use think\Controller;
class Demo extends Controller{

    public function date_demo()
    {
        $date = date('Y-m-d H:i:s',strtotime("-1 month +16 day"));
        echo $date;
    }
    public function array_demo()
    {
        $A = array(1,5,9,6,3,3,4);
        $B = array(1,8,9,6,2,0,7,7,4,3);
//        $C = array_merge($A,$B);
//        $diff = array_diff($C,$B);
//        $diff = array_diff($A,$diff);
        $diff = array_intersect($A,$B);
        var_dump($diff);
    }
}
