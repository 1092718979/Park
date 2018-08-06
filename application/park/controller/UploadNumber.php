<?php
/**
 * Created by PhpStorm.
 * User: Bruce
 * Date: 2018/4/29
 * Time: 10:04
 * 上传手机号，车牌号{openId,telphone,carNumber}
 * 返回{stat:success/fail,message:''}
 */

namespace app\park\controller;

use think\Request;
use app\park\model\UploadNumberMode;

class UploadNumber
{
    private $openId;
    private $carId;
    private $phoneNumber;

    public function index(Request $request)
    {
        $this->initData($request);
        if ($this->checkArgs(['carId', 'phoneNumber']) == 0)
            return;
        $mode = new UploadNumberMode();
        echo json_encode($mode->updateAll($this->openId, $this->carId, $this->phoneNumber));
    }

    private function initData(Request $request)
    {
        $this->openId = $request->post('openId');
        $this->carId = $request->post('carId');
        $this->phoneNumber = $request->post('phoneNumber');
    }

    private function checkArgs(Array $args)
    {
        $mResponse = '';
        if ($this->openId == null) {
            $mResponse .= 'openId is null  ';
        }

        foreach ($args as $v) {
            if ($this->$v == null) {
                $mResponse .= $v . ' is null  ';
            }
        }
        if ($mResponse != null) {
            echo json_encode(['code' => '300', 'result' => $mResponse]);
            return 0;
        } else {
            return 1;
        }
    }

    public function updatePhone(Request $request)
    {
        $this->initData($request);
        if ($this->checkArgs(['phoneNumber']) == 0)
            return;
        $mode = new UploadNumberMode();
        echo json_encode($mode->updatePhoneNumber($this->openId, $this->phoneNumber));
    }

    public function updateCarId(Request $request)
    {
        $this->initData($request);
        if ($this->checkArgs(['carId']) == 0)
            return;
        $mode = new UploadNumberMode();
        echo json_encode($mode->updateCarId($this->openId, $this->carId));
    }
}