<?php
namespace app\park\validate;
/**
 * Created by JiFeng.
 * User: 10927
 * Date: 2018/5/9
 * Time: 19:21
 */


use app\park\exception\BaseExcetion;
use app\park\exception\ParamException;
use think\Exception;
use think\Request;

use think\Validate;

class BaseValidate extends Validate{

    protected $rule = [];

    protected $message =[];

    public function gocheck()
    {
        $request = Request::instance();
        $param = $request->param();
        $result = $this->batch()->check($param);
        if (!$result){
            throw new ParamException([
                'result' => $this->error,
            ]);
        }
        else{
            return true;
        }
    }

    protected function IdMustBePostiveInt($value)
    {
        if (is_numeric($value) && is_int($value + 0) && ($value + 0) > 0) {
            return true;
        } else {
            return false;
        }
    }


}