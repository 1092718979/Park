<?php
/**
 * Created by PhpStorm.
 * User: Bruce
 * Date: 2018/5/9
 * Time: 19:38
 * 获取用户的手机号和车牌号(openId)
 * 返回(phoneNumber,carId)
 */

namespace app\park\controller;

use think\Request;
use app\park\model;

class GetPhoneCar
{
    private $openId;

    public function index(Request $request)
    {
        $this->openId = $request->post('openId');
        if ($this->openId == null) {
            echo json_encode(['code' => '300', 'result' => 'openId is null']);
            return;
        }

        $mode = new model\GetPhoneCarMode();
        $result = $mode->getNumber($this->openId);
        $response=['code'=>'200','result'=>$result];
//        $result['code']='200';
        echo json_encode($response);
    }
}