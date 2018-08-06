<?php
/**
 * Created by PhpStorm.
 * User: Bruce
 * Date: 2018/4/29
 * Time: 10:20
 * 查询订单详情{billId,openId}
 * 返回(parkName,orderStartTime,orderEndTime,orderFee,parkStartTime,
 * parkEndTime,parkFee,parkTimeLength,state)//state订单的状态（已预约，停车中，未付款，完成）
 */

namespace app\park\controller;

use think\Request;
use app\park\model\GetOrderParticularMode;

class GetOrderParticulars
{
    private $openId;
    private $billId;
    private $type;

    public function index(Request $request)
    {
        date_default_timezone_set('PRC');
        $this->openId = $request->post('openId');
        $this->billId = $request->post('billId');
        $this->type = $request->post('type');
        $res = $this->checkArg();
        if ($res != 'ok') {
            return ['code' => '300', 'result' => $res];
        }
        $mode = new GetOrderParticularMode();
        $result = $mode->getOrderParticular($this->billId, $this->type);
        return $result;
    }

    private function checkArg()
    {
        $result = '';
        if ($this->openId == null) {
            $result .= 'openId is null';
        }
        if ($this->billId == null) {
            $result .= 'billId is null';
        }
        if (empty($this->type)) {
            /**
             * 0 停车场
             * 1 路边
             * 2 停车场
             */
            $this->type = 0;

        } else {
            $TYPE = include(APP_PATH . "Enum/BillType.php");
            if ($this->type == $TYPE[0]) {
                $this->type = 0;
            } else if ($this->type == $TYPE[1]) {
                $this->type = 1;
            } else if ($this->type == $TYPE[2]) {
                $this->type = 2;
            } else {
                $this->type = 0;
            }
        }

        if (empty($result)) {
            $result = 'ok';
            return $result;
        } else {
            return $result;
        }
    }
}