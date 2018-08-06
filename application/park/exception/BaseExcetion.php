<?php
/**
 * Created by JiFeng.
 * User: 10927
 * Date: 2018/5/13
 * Time: 10:00
 */

namespace app\park\exception;


use think\Exception;

class BaseExcetion extends Exception{
    //错误状态码
    public $code = 300;
    //错误信息
    public $result = '参数错误';

    public function __construct($params = []) {
        if (!is_array($params)){
            return ;
        }
        if (array_key_exists('code',$params)){
            $this->code = $params['code'];
        }
        if (array_key_exists('result',$params)){
            $this->result = $params['result'];
        }
    }
}