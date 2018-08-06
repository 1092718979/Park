<?php
/**
 * Created by JiFeng.
 * User: 10927
 * Date: 2018/5/20
 * Time: 18:17
 */

namespace app\park\model;


use app\Enum\ParkEnum;
use app\park\exception\ClientException;
use think\Model;

class PayInfo extends Model{

    public static function checkNotPay($openId){
        $bill = new Bill();
        $newBill = $bill->with(['getParkinfo'])
            ->where('open_id','=',$openId)
            ->order('create_time desc')
            ->limit(1)
            ->find();
        $newBill = $newBill->toArray();
        $billId = $newBill['bill_id'];
        $payId = '';
        if (!empty($newBill['order_pay_id'])){
            if (empty($newBill['park_pay_id'])){
                $payId = $newBill['order_pay_id'];
            }  else{
                $payId = $newBill['park_pay_id'];
            }
            $result = self::get($payId);
            $result = $result->toArray();
            $state = $result['state'];
            if ($state == 1 || $state == 4){
                return [
                    'code' => 400,
                    'result' => [
                        'parkId' => $newBill['get_parkinfo']['park_id'],
                        'parkName' => $newBill['get_parkinfo']['park_name'],
                        'date' => $newBill['order_time'],
                        'orderId' => $billId,
                        'state' => $state
                    ]
                ];
            }
        }
        return [
            'code' => 200,
            'result' => '没有检测到未缴费记录',
        ];
    }



    public static function countFee($parkId,$time){
        $park = ParkInfo::get($parkId);
        return ($time*2) * $park->univalence;
    }
}