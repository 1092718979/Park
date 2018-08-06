<?php
/**
 * Created by Bruce.
 * IDE: PhpStorm
 * Date: 2018/7/27
 * Time: 19:33
 */

namespace app\park\model;


use app\Enum\RoadsideEnum;
use think\Db;

class RoadsideUnlockMode
{
    private $db;

    function __construct()
    {
        $this->db = Db::connect();
    }

    public function changeState($billId)
    {
        $enterTime = date("Y-m-d H:i:s", time());
        $values = ['state' => RoadsideEnum::PARKING, 'enter_time' => $enterTime];
        $res = $this->db->table('bill')->where('bill_id', $billId)->update($values);
        if ($res == 1) {
            return true;
        } else {
            return false;
        }
    }

    public function getPhoneNumber($parkId, $spaceId)
    {
        $res = $this->db->table('space_info')->field('number')->where('park_id', $parkId)->where('space_id', $spaceId)->select();
        $res = $res[0];
        $len = strlen($res['number']);
        if ($len == 11) {
            return $res['number'];
        } else {
            return 'number is null';
        }
    }

    function __destruct()
    {
        // TODO: Implement __destruct() method.
        if ($this->db != null) {
            $this->db->close();
        }
    }
}