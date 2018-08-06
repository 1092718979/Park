<?php
/**
 * Created by JiFeng.
 * User: 10927
 * Date: 2018/7/24
 * Time: 10:25
 */

namespace app\park\service;

use app\park\model\BackInfo;
use app\park\model\Bill;
use app\park\model\PayInfo;
use think\Loader;
use think\Log;

Loader::import('wxPay.WxPay', EXTEND_PATH, '.Api.php');

class PayRefund
{

    protected $totalFee;

    /**
     * 退款入口
     *      $billId:订单表ID
     *      $type:true表示预约订单，false表示停车订单
     *      $resont:退款理由
     */
    public function refundPay($billId, $type, $fee, $resont = '')
    {
        //校验订单是否存在
        $this->checkBill($billId, $type);
        $bill = Bill::get($billId);
        $payId = $type ? $bill->order_pay_id : $bill->park_pay_id;
        $payInfo = PayInfo::get($payId);
        $this->totalFee = $payInfo->fee;
        $back = new BackInfo();

        $back->save([
            'back_id' => $this->getBackNo(),
            'open_id' => $payInfo->open_id,
            'bill_id' => $billId,
            'pay_id' => $payId,
            'fee' => $fee,
            'resont' => $resont
        ]);
        $result = $this->makeWxData($back);
        dump($result);
        //保存信息
        $back->save([
            'refund_no' => $result['out_refund_no'],
        ]);
    }

    /**
     * 校验订单
     */
    public function checkBill($billId, $type)
    {

    }

    /**
     * 生成退款单号
     */
    public function getBackNo()
    {
        $str = date('Y') . date('m') . date('d') . date('h') . date('m') . date('s');
        for ($i = 0; $i < 6; $i++) {
            $str .= rand(0, 9);
        }
        return $str;
    }

    /**
     * 生成微信退款数据
     */
    public function makeWxData($back)
    {
        $wxRefund = new \WxPayRefund();
        //退款商品订单号
        $wxRefund->SetOut_trade_no($back->pay_id);
        //退款订单号
        $wxRefund->SetOut_refund_no($back->back_id);
        //订单金额
        $wxRefund->SetTotal_fee(($this->totalFee) * 100);
        //退款金额
        $wxRefund->SetRefund_fee(($back->fee) * 100);
        //
        $wxRefund->SetOp_user_id('1504141111');
        //调用微信退款接口
        return $this->getPayfund($wxRefund);
    }

    /**
     * 调用微信退款接口
     */
    private function getPayfund($wxRefund)
    {
        $wxOrder = \WxPayApi::refund($wxRefund);
        if ($wxOrder['return_code'] != 'SUCCESS' || $wxOrder['result_code'] != 'SUCCESS') {
            Log::init([
                'type' => 'file',
                'path' => LOG_PATH,
                'level' => ['error'],
            ]);
            Log::record($wxOrder, 'error');
            Log::record('获取与支付订单失败', 'error');
        }
        return $wxOrder;
    }
}