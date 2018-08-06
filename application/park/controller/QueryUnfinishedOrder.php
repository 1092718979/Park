<?php
/**
 * Created by PhpStorm.
 * User: Bruce
 * Date: 2018/4/29
 * Time: 10:17
 * .查看是否有未完成的订单{openId}
 * 返回{is:'true,false',orderId,fee}
 */

namespace app\park\controller;
use app\park\model\PayInfo;
use app\park\validate\QueryValidate;

/**
17.查看是否有未完成的订单{openId}
返回{parkId,date，orderId,state}//state订单的状态（已预约，停车中，未付款，完成）
QueryUnfinishedOrder
 */

class QueryUnfinishedOrder
{
    public function QUFOrder(){
        //参数校验
        (new QueryValidate())->gocheck();
        $openId = input('post.openId');
        $result = PayInfo::checkNotPay($openId);
        return $result;
    }
}