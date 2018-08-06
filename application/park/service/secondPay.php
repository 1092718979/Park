<?php
/**
 * Created by JiFeng.
 * User: 10927
 * Date: 2018/7/20
 * Time: 15:07
 */

namespace app\park\service;


use app\Enum\ParkEnum;
use app\park\exception\ClientException;
use app\park\model\Bill;
use app\park\model\PayInfo;
use think\Loader;
use think\Log;
Loader::import('wxPay.WxPay',EXTEND_PATH,'.Api.php');

class secondPay {
    public $payId = '';
    /**
     * 像停车场获取数据
     *      停车时长    停车费用    停车开始结束时间
     */
    public function getFromParking(){
        //调用停车场接口获取数据
        //....
        return [
            'park_time' => '2',
            'park_fee' => '0.1',
            'enter_time' => '2018-07-20 15:59:45',
            'leave_time' => '2018-07-20 15:59:45',
        ];
    }

    /**
     * 判断订单是否符合条件
     */
    public function check($billId){
        $bill = Bill::get($billId);
        if (!$bill){
            throw new ClientException([
                'result' => '无效的订单'
            ]);
        }
        if (!($bill->state == 2 || $bill->state == 3)){
            throw new ClientException([
                'result' => '无效的订单'
            ]);
        }
    }

    /**
     * 创建出场订单
     */
    public function createParkPay($billId,$parkResult){
        $bill = Bill::get($billId);
        $parkPayId = $this->getOutNo();
        $payInfo = new PayInfo();
        $payInfo->save([
            'pay_id' => $parkPayId,
            'open_id' => $bill->open_id,
            'park_id' => $bill->park_id,
            'fee' => $parkResult['park_fee'],
            'state' => 1,
            'type' => 1,
        ]);
        $bill->save([
            'enter_time' => $parkResult['enter_time'],
            'leave_time' => $parkResult['leave_time'],
            'park_pay_id' => $parkPayId,
        ]);
        $payId = $payInfo->pay_id;
        $this->payId = $payId;
        $bill->save([
            'order_pay_id' => $payId
        ]);
        $sign = $this->makeWxPreOrder($payInfo);
        return $sign;
    }

    /**
     * 生成out_trade_no
     */
    public function getOutNo()
    {
        $str = date('Y') . date('m') . date('d') . date('h') . date('m') . date('s');
        for ($i = 0; $i < 6; $i++) {
            $str .= rand(0, 9);
        }
        return $str;
    }

    /**
     * 生成微信预订单
     */
    public function makeWxPreOrder($payInfo)
    {
        $wxOrderDate = new \WxPayUnifiedOrder();
        //商户订单号
        $wxOrderDate->SetOut_trade_no($payInfo->pay_id);
        //交易类型
        $wxOrderDate->SetTrade_type('JSAPI');
        //钱
        $wxOrderDate->SetTotal_fee($payInfo->fee*100);
        //商品描述
        $wxOrderDate->SetBody('停车服务');
        //用户标识
        $wxOrderDate->SetOpenid($payInfo->open_id);
        //回调接口
        $wxOrderDate->SetNotify_url('https://utingche.xin/park/public/park/pay/secondNotify');

        return $this->getPaySign($wxOrderDate);
    }

    /**
     * 调用统一下单接口
     *      修改了WxPayApi   546行
     *      修改了WxPayApi   53，54行
     */
    private function getPaySign($wxOrderDate){
        $wxOrder = \WxPayApi::unifiedOrder($wxOrderDate);
        if ($wxOrder['return_code'] != 'SUCCESS' || $wxOrder['result_code'] != 'SUCCESS'){
            Log::init([
                'type'  => 'file',
                'path'  => LOG_PATH,
                'level' => ['error'],
            ]);
            Log::record($wxOrder,'error');
            Log::record('获取与支付订单失败','error');
        }
        $this->savePrepayId($wxOrder['prepay_id']);
        $sign = $this->getSign($wxOrder);
        return $sign;
    }

    /**
     * 向数据库保存prepay_id
     */
    private function savePrepayId($prepay_id)
    {
        PayInfo::where('pay_id', '=', $this->payId)
            ->update(['prepay_id' => $prepay_id]);
    }

    /**
     * 生成小程序需要的参数
     */
    public function getSign($wxOrder){
        $wxJsApiPay = new \WxPayJsApiPay();
        $wxJsApiPay->SetAppid('wxdf08ee2795813cc1');
        $wxJsApiPay->SetTimeStamp((string)time());
        $wxJsApiPay->SetNonceStr(md5(time() . mt_rand(0,1000)));
        $wxJsApiPay->SetPackage('prepay_id='. $wxOrder['prepay_id']);
        $wxJsApiPay->SetSignType('md5');

        $sign = $wxJsApiPay->MakeSign();
        $payDate = $wxJsApiPay->GetValues();
        $payDate['paySign'] = $sign;
        unset($payDate['appid']);
        return $payDate;
    }
}
