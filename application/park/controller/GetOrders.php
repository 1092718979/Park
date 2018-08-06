<?php
/**
 * Created by PhpStorm.
 * User: Bruce
 * Date: 2018/4/29
 * Time: 10:19
 *查询用户所有订单（预约和停车和一个）{openId,start,nu}
 *返回{parkId,date，orderId,state}//state订单的状态（已预约，停车中，未付款，完成)
 */

namespace app\park\controller;

use think\Request;
use app\park\model\GetOrdersMode;

class GetOrders
{
    private $openId;
    private $start;
    private $nu;

    public function index(Request $request)
    {
        $this->openId = $request->post('openId');
        $this->start = $request->post('start');
        $this->nu = $request->post('nu');
        if ($this->checkout() == 1) {
            return;
        }
        $mode = new GetOrdersMode();
        $result = $mode->getOrders($this->openId, $this->start, $this->nu);

        return (['code'=>'200','result'=>$result]);
    }

    private function checkout()
    {
        $response = '';
        if ($this->openId == null) {
            $response .= 'openId is null';
        }
        if (floor($this->start) != $this->start) {
            $response .= 'start not int';
        }
        if (floor($this->nu) != $this->nu) {
            $response .= 'nu not int';
        }
        if ($response == null) {
            return 0;
        } else {
            echo json_encode(['code' => '300', 'result' => $response]);
            return 1;
        }
    }

}