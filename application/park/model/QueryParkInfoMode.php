<?php
/**
 * Created by PhpStorm.
 * User: MJ
 * Date: 2018/5/24
 * Time: 11:03
 */

namespace app\park\model;

use think\Db;

class QueryParkInfoMode
{

    public function queryParkInfo1($parkId, $type)
    {
        $response = ['code' => 200,];
        $sql1 = "SELECT  park_name,location,longitude,latitude,space,occupied_space,univalence,big_univalence FROM park_info WHERE park_id= ?";
        $db = Db::connect();
        $result1 = $db->query($sql1, [$parkId]);
        $result1[0]['surplus'] = $result1[0]['space'] - $result1[0]['occupied_space'];
        $response['result'] = $result1[0];
        if ($type == 'r') {
            //state 为0时车位可用  为1时车位被占用  不可用
            $STATE = include(APP_PATH . "Enum/SpaceType.php");
            $STATE = $STATE['usable'];
            $sqlR = "SELECT space_id FROM space_info WHERE park_id=? AND state=$STATE ORDER BY space_id ASC ;";
            $result = $db->query($sqlR, [$parkId]);
            if (!empty($result)) {//车位不为空
                $spaces = Array();
                foreach ($result as $value) {
                    array_push($spaces, explode("_", $value['space_id'])[1]);//截取“2_X”中的X
                }
                $response['space'] = $spaces;
            } else {
                $response['space'] = [];
            }
        }

        $db->close();
        return $response;
    }
}