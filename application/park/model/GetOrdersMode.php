<?php
/**
 * Created by PhpStorm.
 * User: Bruce
 * Date: 2018/5/12
 * Time: 9:37
 */

namespace app\park\model;

use think\Db;

class GetOrdersMode
{
    public function getOrders($openId, $start, $nu)
    {
        $db = Db::connect();
        //        $sql = "select park_id,order_time,state from (select park_id,order_time,state from park_bill where open_id=? union all select park_id,order_time,state from roadside_bill where open_id=?)as a order by a.order_time ASC limit ?,?;";

        $sql = "select user_info.car_id,park_info.park_name,bill.bill_id,bill.order_time,bill.state,bill.type,pay1.fee as order_fee,pay2.fee as park_fee from bill join park_info left join pay_info as pay1 on bill.order_pay_id=pay1.pay_id left join pay_info as pay2 on bill.order_pay_id=pay2.pay_id join user_info on bill.park_id=park_info.park_id and user_info.open_id=bill.open_id and bill.open_id=? order by bill.order_time asc limit ?,?;";


        //        $sql = "select park_info.park_name,bill.bill_id,bill.order_time,bill.state from bill join park_info on bill.park_id=park_info.park_id and bill.open_id=? order by bill.order_time asc limit ?,?";
        $result = $db->query($sql, [$openId, $start, $nu]);
        $BILLTYPE = include(APP_PATH . "Enum/BillType.php");

        foreach ($result as &$value) {
            $value['type'] = $BILLTYPE[$value['type']];
        }
        $db->close();
        return $result;
    }
}
