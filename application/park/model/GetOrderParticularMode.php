<?php
/**
 * Created by PhpStorm.
 * User: Bruce
 * Date: 2018/5/12
 * Time: 10:39
 * select p.park_name,b.order_time,b.order_length,b.enter_time,b.leave_time,
 * b.state,pay.fee as order_fee,pay2.fee as park_fee from bill as b join park_info as p
 * on b.park_id=p.park_id and b.bill_id='212122' join pay_info as pay on
 * b.order_pay_id=pay.pay_id join pay_info as pay2 on b.order_pay_id=pay2.pay_id;
 */

namespace app\park\model;

use think\Db;

class GetOrderParticularMode
{
    public function getOrderParticular($billId, $type)
    {
        $sql = "select p.park_id,p.park_name,p.location,p.longitude,p.latitude,b.bill_id,b.space_id,b.order_time,b.order_length,b.enter_time,b.leave_time,b.state,b.type,b.park_pay_id,pay.fee as order_fee,pay2.fee as park_fee from bill as b join park_info as p on b.park_id=p.park_id and b.bill_id=? left join pay_info as pay on b.order_pay_id=pay.pay_id left join pay_info as pay2 on b.park_pay_id=pay2.pay_id;";
        $db = Db::connect();
        $result = $db->query($sql, [$billId]);
        $result = $result[0];
        //处理时间
        date_default_timezone_set('PRC');
        $a = $result;
        $hour = floor((strtotime($a['leave_time']) - strtotime($a['enter_time'])) / 1800);
        $hour = $hour / 2.0;
        $result['stop_time'] = $hour;

        //判断是否是路边停车 如果是处理车位号
        if ($type != 0) {
            $spaceId = $result['space_id'];
            $sps = explode('_', $spaceId);
            $result['space_id'] = $sps[1];
        }
        $debt = '';
        //判断在路边停车时是否有未支付的订单（预交费不够）
        if ($result['state'] == 4 && $result['type'] != 0 && $result['type'] < 3 && !empty($result['park_pay_id'])) {
            $mode = new QueryDebtMode();
            $res = $mode->queryPay($result['park_pay_id']);
            if (!empty($res)) {
                $debt = $res['fee'];
            } else {
                $debt = 'null';
            }
        }
        $BILLTYPE = include(APP_PATH . "Enum/BillType.php");
        $result['type'] = $BILLTYPE[$result['type']];
        $result['debt'] = $debt;
        $response = ['code' => '200', 'result' => $result];
        $db->close();
        return $response;
    }
}
