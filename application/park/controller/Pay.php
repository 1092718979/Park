<?php
/**
 * Created by JiFeng.
 * User: 10927
 * Date: 2018/5/9
 * Time: 19:05
 */

namespace app\park\controller;

use app\Enum\ParkEnum;
use app\park\model\Bill;
use app\park\model\ParkInfo;
use app\park\model\PayInfo;
use app\park\model\SpaceInfo;
use app\park\service\OrderService;
use app\park\service\PayRefund;
use app\park\service\secondPay;
use app\park\service\secondWxNotify;
use app\park\service\WxNotify;
use app\park\validate\PayValidate;
use app\park\service\Pay as PayService;
use think\Db;

class Pay
{
    /**
     *  创建订单
     *      创建支付订单
     * 回掉。
     *      支付成功。
     *          更改订单状态
     *      保存数据，
     * openId,parkId,type:'路边/停车场',space(停车场空),time,state=0,1,2}
     */
    public function firstPay()
    {
        //参数校验
        (new PayValidate())->gocheck();
        //获取参数
        $openId = input('post.openId');
        $parkId = input('post.parkId');
        $type = input('post.type');
        $space = $type == '路边' ? input('post.space') : "";
        //车位数检车
        $orderService = new OrderService();
        $orderService->checkRemain($parkId, $space);
        //生成两个订单
        $result = $orderService->BuildOrder();
        //向微信服务器获取数据
        $payService = new PayService($openId, $result['billId'], $result['payId']);

        $payData = $payService->pay();
        return [
            'code' => 200,
            'result' => [
                'billId' => $result['billId'],
                'payData' => $payData,
            ]
        ];
    }

    public function receiveNotify()
    {
        $notity = new WxNotify();
        $notity->Handle();
    }

    /**
     * 出场缴费
     *      billId
     *
     */
    public function secondPay()
    {
        $sPay = new secondPay();
        //获取数据
        $billId = input('post.billId');
        //判断订单是否符合条件
        $sPay->check($billId);
        //从对方服务器中获取出数据
        $parkResult = $sPay->getFromParking();
        //创建出场支付订单
        $payData = $sPay->createParkPay($billId, $parkResult);
        return [
            'code' => 200,
            'result' => [
                'payData' => $payData,
            ]
        ];
    }

    public function secondNotify()
    {
        $notity = new secondWxNotify();
        $notity->Handle();
    }

    /**
     * 本地测试用接口
     */
    public function sss()
    {
        /*$data = [
            'result_code' => 'SUCCESS',
            'bank_type' => 'CFT',
            'out_trade_no' => '20180721040759173064',
            'total_fee' => '2',
            'transaction_id' => '4200000114201807205980687904',
        ];
        if ($data['result_code'] == 'SUCCESS') {
            $payId = $data['out_trade_no'];
                //生成当前时间
                date_default_timezone_set('PRC');
                $nowTime = date("Y-m-d h:i:s");
                //更新payInfo
                $payInfo = PayInfo::get($payId);
                $payInfo->save(['pay_time' => $nowTime, 'bank_type' => $data['bank_type'], 'transaction_id' => $data['transaction_id'], 'state' => 2,]);
                //更新订单表
                $bill = new Bill();
                $newBill = $bill->where('order_pay_id', '=', $payId)->find();
                $newBill->save(['order_time' => $nowTime, 'state' => ParkEnum::ORDER_COMPLETE,]);
                //扣除车位
                $parkId = $newBill->park_id;
                $spaceId = $newBill->space_id;
                $orders = new OrderService();
                $orders->checkRemain($parkId, $spaceId);
                $park = ParkInfo::get($parkId);
                $num = $park->occupied_space;
                $park->occupied_space = $num + 1;
                if ($newBill->type == 1) {
                    $spaceInfo = SpaceInfo::get($spaceId);
                    $spaceInfo->state = 1;
                    $spaceInfo->save();
                }
                $park->save();
                return true;

        }*/
        $refund = new PayRefund();
        $refund->refundPay(15323964406701, false, 0.09);

    }
}