<?php
/**
 * Created by PhpStorm.
 * User: Bruce
 * Date: 2018/4/29
 * Time: 10:35
 */
namespace app\park\model;

use think\Db;

class UpdateUserInfoMode
{
    public function update($openId, $userName, $sessionKey)
    {
        $db = Db::connect();
        $count = $db->table('user_info')->where('open_id', $openId)->count();
        if ($count == 0) {
            $values = ['open_id' => $openId, 'user_name' => $userName, 'session_key' => $sessionKey];
            $res = $db->table("user_info")->insert($values);
//            echo $res . 'insert';
            $db->close();
            if ($res == 1) {
                return ['code' => '200', 'result' => 'success'];
            } else {
                return ['code' => '200', 'result' => 'fail'];
            }
        } else {
            $values = ['user_name' => $userName, 'session_key' => $sessionKey];
            $res = $db->table("user_info")->where('open_id', $openId)->update($values);
            $result = $db->table('user_info')->field('phone_number,car_id')->where('open_id', $openId)->find();
            $db->close();

            $result2 = ['code' => '200', 'result' => '', 'phone_number' => $result['phone_number'], 'car_number' => $result['car_id']];
            if ($res == 1) {
                $result2['result'] = 'success';
                return $result2;
            } else {
                $result2['result'] = 'fail';
                return $result2;
            }
        }
    }
}