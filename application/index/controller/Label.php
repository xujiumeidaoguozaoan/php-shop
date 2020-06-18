<?php
namespace app\index\controller;

use app\index\model\Label as LabelModel;
use app\index\controller\Common;
class Label extends Common{
    public function lst($label)
    {
        $data = LabelModel::lst($label);
        $this->assign('lst',$data);
        return view();
    }
}