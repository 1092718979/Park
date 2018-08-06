<?php
/**
 * Created by PhpStorm.
 * User: Bruce
 * Date: 2018/4/29
 * Time: 10:10
 * 预约停车场/路边完成{openId,parkId,stat:'路边/停车场',space(停车场空),time,orderId}
 * 返回{stat:success/fail,message:''}
 */
namespace app\park\controller;
use app\park\model\PayInfo;
use app\park\service\OrderService;
use app\park\validate\OrderValidate;
header('Content-type:text/html;charset=GBK');

/**
8.预约（停车场/路边）完成{openId,parkId,type:'路边/停车场',space(停车场空),stat:'预约/出场'}
返回{stat:success/fail,fee，orderId}

 * 1.检查未付款订单
 *      订单表，支付表
 * 2.检查库存，
 *      通过生成订单表
 *          停车场使用数+1，（），启动2分钟计时，之后判断是否完成
 *              如果完成，不做处理
 *              未完成，删除订单，退还车位数
 *      不，库存不足。
 */
class OrderParks
{
    private $openId;
    private $parkId;
    private $stat;
    private $space = '';
    private $time;
    public function OrderParks(){
        //验证器
        (new OrderValidate())->gocheck();
        //获取参数
        $this->openId = input('post.openId');
        $this->parkId = input('post.parkId');
        $this->space = input('post.space');
        $this->stat = input('post.stat');
        $this->time = input('post.time');

        //检测库存
        OrderService::checkRemain($this->parkId,$this->stat,$this->space);
        //生成订单
        $orderId = OrderService::BuildOrder();
        //计算费用
        $fee = PayInfo::countFee($this->parkId,$this->time);

        return [
            'code' => 200,
            'result' => [
                'fee' => $fee,
                'orderId' => $orderId,
            ],
        ];
    }
}