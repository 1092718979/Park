<?php
/**
 * Created by Bruce.
 * IDE: PhpStorm
 * Date: 2018/7/28
 * Time: 9:27
 *
 * 查询是否有路边没有支付的订单（预交费不足）
 */

namespace app\park\model;


use think\Db;

class QueryDebtMode
{
    private $db;

    public function __construct()
    {
        $this->db = Db::connect();
    }

    public function query($openId)
    {
        $sql = "SELECT bill_id,park_pay_id,state,type FROM bill WHERE (state>1 AND  state <5 AND open_id=?);";
        $res = $this->db->query($sql, [$openId]);
        $result = ['code' => '200'];
        if (!empty($res)) {
            $res = $res[0];
            $TYPE = include(APP_PATH . "Enum/BillType.php");
            //路边需要state到3才显示
            if ($res['type'] == 2 && $res['state'] > 2) {
                $result['msg'] = 'ok';
                $res['type'] = $TYPE[$res['type']];
                $result ['result'] = $res['type'];
                $result ['bill'] = $res['bill_id'];
                $result['typeCode'] = $res['state'];
                $result ['debt'] = $this->queryPay($res['park_pay_id']);
            } else {
                $result['msg'] = 'ok';
                $res['type'] = $TYPE[$res['type']];
                $result ['result'] = $res['type'];
                $result ['bill'] = $res['bill_id'];
                $result['typeCode'] = $res['state'];
                $result ['debt'] = $this->queryPay($res['park_pay_id']);
            }
        } else {
            $result['msg'] = 'null';
            $result ['debt'] = 'no debt';
        }
        return $result;
    }

    public function queryPay($payId)
    {
        if (!empty($payId)) {
            $result = $this->db->table('pay_info')->field('pay_id,fee')->where('pay_id', $payId)->select();
            if (!empty($result)) {
                return $result[0];
            } else {
                return null;
            }
        } else {
            return null;
        }

    }

    public function __destruct()
    {
        // TODO: Implement __destruct() method.

        if ($this->db != null) {
            $this->db->close();
        }
    }
}