<?php
namespace app\park\validate;
/**
 * Created by JiFeng.
 * User: 10927
 * Date: 2018/5/9
 * Time: 19:22
 */
class QueryValidate extends BaseValidate{
    protected $rule = [
        'openId' => 'require|length:28|alphaNum'
    ];

    protected $message = [
        'openId' => 'openid参数异常',
    ];
}