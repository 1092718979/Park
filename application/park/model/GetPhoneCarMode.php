<?php
/**
 * Created by PhpStorm.
 * User: Bruce
 * Date: 2018/5/9
 * Time: 19:44
 */

namespace app\park\model;

use think\Db;

class GetPhoneCarMode
{
    public function getNumber($openId)
    {
        $db = Db::connect();
        $result = $db->table('user_info')->field('phone_number,car_id')->where('open_id', $openId)->find();
        $db->close();
        return $result;
    }

}