<?php
namespace app\park\validate;
/**
 * Created by JiFeng.
 * User: 10927
 * Date: 2018/5/9
 * Time: 19:19
 */

use \think\Exception;

/**
8.预约（停车场/路边）完成{openId,parkId,stat:'路边/停车场',space(停车场空),time}
返回{stat:success/fail,fee，orderId}
OrderParks
 */

class OrderValidate extends BaseValidate{
    protected $rule = [
        'openId' => 'require|length:28|alphaNum',
        'parkId' => 'require|IdMustBePostiveInt',
        'time' => 'require',
    ];

    protected $message = [
        'openId' => 'openId参数必须是28位，字母与数字',
        'parkId' => 'parkId必须是正整数',
        'time' => 'time异常',
    ];

    protected $spaceRule = [
        'space' => 'require',
    ];

    protected $spaceMsg = [
        'space' => '路边停车参数异常',
    ];

    public function statJudge($value,$rule='',$data='',$field=''){
        if ($value == '路边'){
            $validate = new BaseValidate();
            $validate->rule = $this->spaceRule;
            $result = $validate->check(['space' => $data['space']]);
            if (!$result) {
                $this->message['stat'] = $this->spaceMsg['space'];
                return false;
            }
        }
        return true;
    }
}