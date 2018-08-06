<?php
/**
 * Created by JiFeng.
 * User: 10927
 * Date: 2018/5/13
 * Time: 9:20
 */

namespace app\park\service;


use app\Enum\ParkEnum;
use app\park\exception\ClientException;
use app\park\model\Bill;
use app\park\model\ParkInfo;
use app\park\model\PayInfo;
use think\Loader;
use think\Log;

Loader::import('wxPay.WxPay',EXTEND_PATH,'.Api.php');

/**
 * openId,parkId,stat:'路边/停车场',space(停车场空)
 */
class Pay {
    private $openId;
    private $billId;
    private $payId;
    function __construct($openId,$billId,$payId) {
        $this->openId = $openId;
        $this->billId = $billId;
        $this->payId = $payId;
    }

    public function pay(){
        $payInfo = PayInfo::get($this->payId);
        $sign = $this->makeWxPreOrder($payInfo);
        return $sign;
    }

    /**
     * 生成微信预订单
     */
    private function makeWxPreOrder($payInfo){
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
        $wxOrderDate->SetOpenid($this->openId);
        //回调接口
        $wxOrderDate->SetNotify_url('https://utingche.xin/park/public/park/pay/receiveNotify');

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
     * 生成小程序需要的参数
     */
    public function getSign($wxOrder){
        $wxJsApiPay = new \WxPayJsApiPay();
        $wxJsApiPay->SetAppid('wxdf08ee2795813cc1');
        $wxJsApiPay->SetTimeStamp((string)time());
        $wxJsApiPay->SetNonceStr('asdfghjklqwertyuiopzxcv');
        $wxJsApiPay->SetPackage('prepay_id='. $wxOrder['prepay_id']);
        $wxJsApiPay->SetSignType('MD5');

        $sign = $wxJsApiPay->MakeSign();
        $payDate = $wxJsApiPay->GetValues();
        $payDate['paySign'] = $sign;
        unset($payDate['appid']);
        return $payDate;
    }
    /**
     * 向数据库保存prepay_id
     */
    private function savePrepayId($prepay_id){
        PayInfo::where('pay_id','=',$this->payId)
            ->update(['prepay_id' => $prepay_id]);
    }

    /*
     * 判断订单是否有效 并找到payId
     */
    private function checkOrderVaild(){
        $bill = Bill::get($this->billId);
        if (!$bill){
            throw new ClientException([
                'result' => '没有检测到当前订单',
            ]);
        }
        if ($bill->open_id != $this->openId){
            throw new ClientException([
                'result' => '订单信息与客户信息冲突',
            ]);
        }
        if ($bill->state != ParkEnum::ORDER_NOT_PAY && $bill->state != ParkEnum::LEAVE_NOT_PAY){
            throw new ClientException([
                'result' => '当前订单已被支付',
            ]);
        }
        return true;
    }


}
