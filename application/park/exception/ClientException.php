<?php
/**
 * Created by JiFeng.
 * User: 10927
 * Date: 2018/5/13
 * Time: 10:39
 */

namespace app\park\exception;


class ClientException extends BaseExcetion{
    //错误状态码
    public $code = 400;
    //错误信息
    public $result = '客户端错误';
}