<?php
/**
 * Created by JiFeng.
 * User: 10927
 * Date: 2018/5/13
 * Time: 10:45
 */

namespace app\park\exception;


class ParamException extends BaseExcetion{
    //错误状态码
    public $code = 300;
    //错误信息
    public $result = '参数错误';
}