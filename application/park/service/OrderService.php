<?php
namespace app\park\service;
use app\Enum\ParkEnum;
use app\park\exception\ClientException;
use app\park\exception\ParamException;
use app\park\exception\ServiceException;
use app\park\model\Bill;
use app\park\model\ParkBill;
use app\park\model\ParkInfo;
use app\park\model\PayInfo;
use app\park\model\RoadsideSpaceInfo;
use app\park\model\SpaceInfo;
use think\Db;
use think\Exception;

/**
 * Created by JiFeng.
 * User: 10927
 * Date: 2018/5/12
 * Time: 19:35
 */
class OrderService {
    private $billId;
    /**
     * 库存检测
     */
    public function checkRemain($parkId,$space){
        $park = ParkInfo::get($parkId);
        if ($park->type == 1){
            $spaceInfo = SpaceInfo::get($space);
            if (!$spaceInfo){
                throw new ClientException([
                    'result' => '不存在的车位号'
                ]);
            }
            $count = 1 - $spaceInfo->state;
        }else{
            $count = $park->space - $park->occupied_space;
        }
        if ($count <= 0){
            throw new ServiceException([
                'result' => '剩余车位不足',
            ]);
        }
        return true;
    }

    /**
     * 创建订单
     *      bill
     *          type    路边1，停车场0
     *          state   付款状态
     *      pay
     *          type    预约0 出场1
     *          state   1未付款    2已付款
     *      生成bill
     *      生成payinfo
     */
    public function BuildOrder(){
        $openId = input('post.openId');
        $parkId = input('post.parkId');
        $type = input('post.state');
        $time = input('post.time');
        $this->billId = self::orderNO();
        try{
            Db::startTrans();
            $bill = new Bill();
            $bill->save([
                'bill_id' => $this->billId,
                'open_id' => $openId,
                'park_id' => $parkId,
                'order_length' =>$time,
                'state' => ParkEnum::ORDER_NOT_PAY,
                'type' => $type
            ]);
            if ($type != 0) {
                $bill->save([
                    'space_id' => input('post.space'),
                ]);
            }
            $park = ParkInfo::get($parkId);
            if ($park->space - $park->occupied_space < 0){
                Db::rollback();
                throw new ServiceException([
                    'result' => '当前停车场剩余车位不足',
                ]);
            }
            Db::commit();
        }catch (Exception $e){
            throw $e;
        }

        $bill = Bill::get($this->billId);
        $park = ParkInfo::get($bill->park_id);
        $price = $park->univalence;
        //创建Pay记录
        $fee = $type == 0 ? ($bill->order_length * $price) : 0.11;
        $payInfo = new PayInfo();
        $payInfo->save([
            'pay_id' => $this->getOutNo(),
            'open_id' => $openId,
            'park_id' => $bill->park_id,
            'fee' => $fee,
            'state' => 1,
            'type' => 0,
        ]);
        $payId = $payInfo->pay_id;
        $bill->save([
            'order_pay_id' => $payId
        ]);
        return [
            'billId' => $this->billId,
            'payId' => $payId,
        ];
    }

    /**
     * 生成订单号
     * @param $porr
     * @return string
     */
    private static function orderNO(){
        while (1){
            $billId = time();
            for ($i = 0; $i < 4; $i++){
                $billId .= rand(0,9);
            }
            $checkBill = Bill::get($billId);
            if (!$checkBill){
                return $billId;
            }
        }
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
}