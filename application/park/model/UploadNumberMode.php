<?php
/**
 * Created by PhpStorm.
 * User: Bruce
 * Date: 2018/4/29
 * Time: 10:35
 */
namespace app\park\model;

use think\Db;

class UploadNumberMode
{
    public function updateAll($openId, $carId, $phoneNumber)
    {
        $db = Db::connect();
        $data = ['car_id' => $carId, 'phone_number' => $phoneNumber];
        $res = $db->table('user_info')->where('open_id', $openId)->update($data);
        $db->close();
        if ($res == 1) {
            return ['code' => '200', 'result' => 'success'];
        } else {
            return ['code' => '200', 'result' => 'fail'];
        }
    }

    public function updatePhoneNumber($openId, $phoneNumber)
    {
        $db = Db::connect();
        $data = ['phone_number' => $phoneNumber];
        $res = $db->table('user_info')->where('open_id', $openId)->update($data);
        $db->close();
        if ($res == 1) {
            return ['code' => '200', 'result' => 'success'];
        } else {
            return ['code' => '200', 'result' => 'fail'];
        }
    }

    public function updateCarId($openId, $carId)
    {
        $db = Db::connect();
        $data = ['car_id' => $carId];
        $res = $db->table('user_info')->where('open_id', $openId)->update($data);
        $db->close();
        if ($res == 1) {
            return ['code' => '200', 'result' => 'success'];
        } else {
            return ['code' => '200', 'result' => 'fail'];
        }
    }
}