<?php
namespace app\park\validate;
/**
 * Created by JiFeng.
 * User: 10927
 * Date: 2018/5/9
 * Time: 19:15
 */

/**
 * openId,parkId,type:'路边/停车场',space(停车场空),stat:'预约/出场
 */
class PayValidate extends BaseValidate{
    protected $rule = [
        'openId' => 'require|length:28',
        'parkId' => 'require|IdMustBePostiveInt',
        'type' => 'require|chs|statJudge',
        'time' => 'require'
    ];

    protected $message = [
        'openId' => 'openId参数必须是28位，字母与数字',
        'parkId' => 'parkId参数错误',
        'type' => 'parkId必须是正整数',
        'time' => '未传递预约时长'
    ];

    protected $spaceRule = [
        'space' => 'require',
    ];

    protected $spaceMsg = [
        'space' => '路边停车参数异常',
    ];

    public function statJudge($value,$rule='',$data='',$field=''){
        if ($value == '路边') {
            $validate = new BaseValidate();
            $validate->rule = $this->spaceRule;
            $result = $validate->check(['space' => $data['space']]);
            if (!$result) {
                $this->message['type'] = $this->spaceMsg['space'];
                return false;
            }
        }
        return true;
    }
}