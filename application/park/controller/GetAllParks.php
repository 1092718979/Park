<?php
/**
 * Created by PhpStorm.
 * User: Bruce
 * Date: 2018/4/29
 * Time: 10:05
 * 获取所有停车场及路边
 * 返回[{parkId,parkName,longitude,latitude，type}]
 */

namespace app\park\controller;

use app\park\model\GetAllParksMode;

class GetAllParks
{
//    private $response;
    private $TYPE;

    public function index()
    {
        $this->TYPE = include(APP_PATH . "Enum/ParkType.php");//获取数组
        $mode = new GetAllParksMode();
        $result = $mode->getAllParks1();
        foreach ($result as &$value) {
            if ($value['type'] == 1) {//0是停车场，1是路边
                $value['type'] = $this->TYPE[1];
            } else {
                $value['type'] = $this->TYPE[0];
            }
        }
        return $result;
    }

}