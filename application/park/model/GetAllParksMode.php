<?php
/**
 * Created by PhpStorm.
 * User: MJ
 * Date: 2018/5/14
 * Time: 16:56
 */

namespace app\park\model;

use think\Db;

class GetAllParksMode
{
    public function getAllParks1()
    {
        $sql1 = "select park_id,park_name,location,type,longitude,latitude from park_info";
        $db = Db::connect();
        $result1 = $db->query($sql1);
        return $result1;
    }
      /*  public function getAllparks2($parkId){
        $sql2 = "select parkName,longitude,latitude,from park_info where parkId = ? and type =0 ";
        $db = Db::connect();
        $result2 = $db->query($sql2,[$parkId]);
        return $result2;
    }*/



}