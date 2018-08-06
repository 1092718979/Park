<?php
/**
 * Created by JiFeng.
 * User: 10927
 * Date: 2018/7/26
 * Time: 17:30
 */

namespace app\park\service;


use app\Enum\ParkEnum;
use app\park\exception\ClientException;
use app\park\model\Bill;
use app\park\model\ParkInfo;
use app\park\model\PayInfo;
use app\park\model\SpaceInfo;

class roadPay
{
    /**
     * 校验订单
     */
    public function check($bill)
    {
        $check = true;
        if (!$bill) {
            $check = false;
        }
        if ($bill->state == 1) {
            $check = false;
        }
        $space = $bill->space_id;
        $spaceInfo = SpaceInfo::get($space);
        if ($spaceInfo->state == 0) {
            $check = false;
        }
        if (!$check) {
            throw new ClientException(['result' => '订单状态异常',]);
        }
    }

    /**
     * 执行退场动作
     */
    public function leave($bill)
    {
        $space = $bill->space_id;
        $payId = $bill->order_pay_id;
        $payInfo = PayInfo::get($payId);
        $parkId = $bill->park_id;
        $parkInfo = ParkInfo::get($parkId);
        date_default_timezone_set('PRC');
        $nowTime = date("Y-m-d H:i:s");
        $firstTime = $bill->order_time;
        $totalTime = ceil((strtotime($nowTime) - strtotime($firstTime)) / 1800);
        $totalFee = $totalTime * $parkInfo->univalence;
        $payFee = $payInfo->fee - $totalFee;
        dump($nowTime);
        dump($firstTime);
        dump($payFee);

        if ($payFee > 0) {
            //退款流程
            $this->refund($bill->bill_id, $payFee, $nowTime);
        } else if ($payFee < 0) {
            dump('缴费流程');
            //缴费流程
            $this->leavePay($bill->bill_id, abs($payFee), $nowTime);
        }
        //返还车位
        $spaceInfo = SpaceInfo::get($space);
        $spaceInfo->save([
            'state' => 0,
        ]);
        $num = $parkInfo->occupied_space;
        $parkInfo->save([
            'occupied_space' => $num - 1
        ]);
    }

    /**
     * 退款流程
     */
    public function refund($bill_id, $fee, $nowTime)
    {
        $refund = new PayRefund();
        $refund->refundPay($bill_id, true, $fee, '路边停车，超出部分');
        $bill = Bill::get($bill_id);
        $bill->save([
            'leave_time' => $nowTime,
            'state' => 5,
        ]);
    }

    /**
     * 缴费流程
     */
    public function leavePay($billId, $fee, $nowTime)
    {
        $bill = Bill::get($billId);
        $pay = new secondPay();
        $parkPayId = $pay->getOutNo();
        $payInfo = new PayInfo();
        $payInfo->save([
            'pay_id' => $parkPayId,
            'open_id' => $bill->open_id,
            'park_id' => $bill->park_id,
            'fee' => $fee,
            'state' => 1,
            'type' => 1,
        ]);
        $bill->save([
            'enter_time' => $nowTime,
            'leave_time' => $nowTime,
            'state' => ParkEnum::LEAVE_NOT_PAY,
            'park_pay_id' => $parkPayId,
        ]);
        $payId = $payInfo->pay_id;
        $bill->save([
            'park_pay_id' => $payId,
        ]);
    }
}
