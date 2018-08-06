<?php
/**
 * Created by JiFeng.
 * User: 10927
 * Date: 2018/7/26
 * Time: 15:01
 */

namespace app\park\controller;

use app\park\exception\ClientException;
use app\park\model\Bill;
use app\park\model\PayInfo;
use app\park\service\roadPay as roadService;
use app\park\service\secondPay;

class RoadPay
{

    /*
     * 路边——离开（地磁主动调用）
     *      参数车位号
     */
    public function leave()
    {
        $spaceId = input('post.space');
        if (!$spaceId) {
            throw new ClientException([
                'result' => '车位号不存在'
            ]);
        }
        $bill = Bill::where([
            'space_id' => $spaceId,
            'state' => 3,
        ])->find();
        $roadPay = new roadService();
        //校验订单是否存在
        $roadPay->check($bill);
        //退款或付款
        $roadPay->leave($bill);
    }

    /**
     * 等待支付
     */
    public function waitPay()
    {
        $billId = input('post.billId');
        $bill = Bill::get($billId);
        $parkPayId = $bill->park_pay_id;
        $payInfo = PayInfo::get($parkPayId);
        $pay = new secondPay();
        $payData = $pay->makeWxPreOrder($payInfo);
        return [
            'code' => 200,
            'result' => [
                'payData' => $payData,
            ]
        ];
    }
}
