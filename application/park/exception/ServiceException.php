<?php
/**
 * Created by JiFeng.
 * User: 10927
 * Date: 2018/5/13
 * Time: 10:42
 */

namespace app\park\exception;


class ServiceException extends BaseExcetion{
    //错误状态码
    public $code = 500;
    //错误信息
    public $result = '系统错误';
}