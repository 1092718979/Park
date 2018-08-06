<?php
/**
 * Created by JiFeng.
 * User: 10927
 * Date: 2018/5/22
 * Time: 21:18
 */

namespace app\park\service;
use app\Enum\ParkEnum;
use app\park\model\Bill;
use app\park\model\ParkInfo;
use app\park\model\PayInfo;
use app\park\model\SpaceInfo;
use think\Db;
use think\Exception;
use think\Loader;
use think\Log;

Loader::import('wxPay.WxPay',EXTEND_PATH,'.Api.php');

class WxNotify extends \WxPayNotify{
    public $billId;

    /**
     * @param array $data
     * @param string $msg
     * TODO 用户基础该类之后需要重写该方法，成功的时候返回true，失败返回false
     * [ data ] array (
     * 216   'appid' => 'wxdf08ee2795813cc1',
     * 217   'bank_type' => 'CFT',
     * 218   'cash_fee' => '2',
     * 219   'fee_type' => 'CNY',
     * 220   'is_subscribe' => 'N',
     * 221   'mch_id' => '1504141111',
     * 222   'nonce_str' => 'vb9agx82kx17q37upceox4n60jy8jr81',
     * 223   'openid' => 'ovgcr5FrKhzvTX7RibJEeDiqhwVo',
     * 224   'out_trade_no' => '20180720110740290527',
     * 225   'result_code' => 'SUCCESS',
     * 226   'return_code' => 'SUCCESS',
     * 227   'sign' => 'D8774EFBC8198694BA1DE469BBBA49DE',
     * 228   'time_end' => '20180720113450',
     * 229   'total_fee' => '2',
     * 230   'trade_type' => 'JSAPI',
     * 231   'transaction_id' => '4200000114201807205980687904',
     * 232 )
     */
    public function NotifyProcess($data, &$msg)
    {
        if ($data['result_code'] == 'SUCCESS') {
            $payId = $data['out_trade_no'];
            try {
                Db::startTrans();
                //更新payInfo
                $payInfo = PayInfo::get($payId);
                if ($payInfo->state == 1) {
                    //生成当前时间
                    date_default_timezone_set('PRC');
                    $nowTime = date("Y-m-d h:i:s");
                    $payInfo->save([
                        'pay_time' => $nowTime,
                        'bank_type' => $data['bank_type'],
                        'transaction_id' => $data['transaction_id'],
                        'state' => 2,
                    ]);
                    //更新订单表
                    $bill = new Bill();
                    $newBill = $bill
                        ->where('order_pay_id', '=', $payId)
                        ->find();
                    $this->billId = $newBill->bill_id;
                    $newBill->save([
                        'order_time' => $nowTime,
                        'state' => ParkEnum::ORDER_COMPLETE,
                    ]);
                    //扣除车位
                    $parkId = $newBill->park_id;
                    $spaceId = $newBill->space_id;
                    $orders = new OrderService();
                    $orders->checkRemain($parkId, $spaceId);
                    $park = ParkInfo::get($parkId);
                    $num = $park->occupied_space;
                    $park->occupied_space = $num + 1;
                    if ($newBill->type != 0) {
                        $spaceInfo = SpaceInfo::get($spaceId);
                        $spaceInfo->state = 1;
                        $spaceInfo->save();
                    }
                    $park->save();
                }
                Db::commit();
                return true;
            } catch (Exception $e) {
                $payrefund = new PayRefund();
                $payrefund->refundPay($this->billId, false, PayInfo::get($payId)->fee, '停车场车位已满');
                Db::rollback();
                Log::info([
                    'type' => 'file',
                    'path' => LOG_PATH,
                    'level' => ['error'],
                ]);
                Log::record($e, '回掉错误');
            }
        }
        return true;
    }
}
