<?php


namespace app\api\Validate;




class IDCollection extends BaseValidate
{
    protected $rule=[
        'ids' => 'require|checkIDs' ,
    ];
    protected $message  = [
        'ids'=>'ids必须是以，分隔的多个正整数',
    ];
    protected function checkIDs($value)
    {
        $values = explode(',',$value);
        if(empty($values)) return false;
        foreach ($values as $id){
            if(!$this->isPositiveInt($id)){
                return false;
            }
        }
        return true;
    }
}