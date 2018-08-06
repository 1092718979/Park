<?php
/**
 * Created by JiFeng.
 * User: 10927
 * Date: 2018/5/20
 * Time: 18:16
 */

namespace app\park\model;


use think\Model;

class Bill extends Model{
    protected $autoWriteTimestamp = true;
    protected $updateTime = false;
    public function getParkinfo(){
        return $this->belongsTo('ParkInfo','park_id','park_id');
    }
}