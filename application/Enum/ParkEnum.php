<?php
/**
 * Created by JiFeng.
 * User: 10927
 * Date: 2018/5/12
 * Time: 9:27
 */

namespace app\Enum;


class ParkEnum {
    //预约没有付款
    const ORDER_NOT_PAY = 1;
    //完成预约
    const ORDER_COMPLETE= 2;
    //停车中
    const PARKING = 3;
    //离开未付款
    const LEAVE_NOT_PAY = 4;
    //离开付款
    const LEAVE_COMPLETE = 5;
}