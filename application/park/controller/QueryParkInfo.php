<?php
/**
 * Created by PhpStorm.
 * User: Bruce
 * Date: 2018/4/29
 * Time: 10:06
 * 查询停车场的详细信息{parkId}
 *返回{parkId,stat:'路边/停车场',totalNum,spaceNum,parkFee,orderFee}
 */

namespace app\park\controller;

use think\Request;
use app\park\model\QueryParkInfoMode;

class QueryParkInfo
{
    private $parkId;
    private $type;

    public function index(Request $request)
    {
        $this->parkId = $request->post('parkId');
        $this->type = $request->post('type');
        $result = $this->checkArgs();
        if ($result != 'ok') {
            return ['code' => '300', 'result' => $result];
        }
        $mode = new QueryParkInfoMode();
        $result1 = $mode->queryParkInfo1($this->parkId, $this->type);
        if (empty($result1)) {
            $msg = "未查询到改停车场信息";
            $this->returnError($msg);
        }
        return $result1;
    }

    private function checkArgs()
    {
        $result = '';
        if (empty($this->parkId)) {
            $result .= 'parkId is null';
        }
        if (empty($this->type)) {
            $this->type = 'p';
        }

        if (empty($result)) {
            return 'ok';
        } else {
            return $result;
        }
    }

    public function returnError($msg = "")
    {
        $result = array('code' => '0', 'msg' => $msg);
        return $result;
    }
}