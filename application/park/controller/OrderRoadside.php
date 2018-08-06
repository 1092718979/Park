<?php
/**
 * Created by Bruce.
 * IDE: PhpStorm
 * Date: 2018/7/25
 * Time: 11:28
 * 预约路边车位｛
 * 参数：｛openId,parkId,spaceId,orderTime｝
 * ｝
 */

namespace app\park\controller;


use think\Request;

class OrderRoadside
{
    private $openId;
    private $parkId;
    private $spaceId;

    public function index(Request $request)
    {
        $this->openId = $request->post('openId');
        $this->parkId = $request->post('parkId');
        $this->spaceId = $request->post('spaceId');

        $result = $this->checkArgs();
        if ($result != 'ok') {
            return ['code' => '300',
                'result' => $result];
        }
    }

    private function checkArgs()
    {
        $result = '';
        if (empty($this->openId)) {
            $result = 'openId is null';
        }
        if (empty($this->parkId)) {
            $result .= 'parkId is null';
        }
        if (empty($this->spaceId)) {
            $result .= 'spaceId is null';

        }

        if (empty($result)) {
            return 'ok';
        }
        return $result;
    }

}