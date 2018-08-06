<?php
/**
 * Created by JiFeng.
 * User: 10927
 * Date: 2018/7/18
 * Time: 8:46
 */

/**
 * 检测停车长是否有剩余
 *      需要的数据  parkId spaceId
 */
namespace app\park\controller;


use app\park\exception\ClientException;
use app\park\exception\ParamException;
use app\park\exception\ServiceException;
use app\park\model\ParkInfo;
use app\park\model\SpaceInfo;

class CheckPark {

    public static function CheckPark(){
        $parkId = input('post.parkId');
        if (!$parkId){
            throw new ParamException();
        }
        $park = ParkInfo::get($parkId);
        if ($park->type == 1){
            $space = input('post.spaceId');
            $spaceInfo = SpaceInfo::get($space);
            if (!$spaceInfo){
                throw new ClientException([
                    'result' => '不存在的车位号'
                ]);
            }
            $count = 1 - $spaceInfo->state;
        }else{
            $count = $park->space - $park->occupied_space;
        }
        if ($count <= 0){
            throw new ServiceException([
                'result' => '剩余车位不足',
            ]);
        }
        return [
            'code' => 200
        ];
    }
}