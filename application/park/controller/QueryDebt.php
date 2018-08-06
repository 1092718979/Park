<?php
/**
 * Created by Bruce.
 * IDE: PhpStorm
 * Date: 2018/7/30
 * Time: 9:01
 */

namespace app\park\controller;


use app\park\model\QueryDebtMode;
use think\Request;

class QueryDebt
{
    private $openId;

    public function index(Request $request)
    {
        $this->openId = $request->post('openId');
        $res = $this->checkArgs();
        if ($res != 'ok') {
            return ['code' => '200', 'result' => 'open id is null'];
        }
        $mode = new QueryDebtMode();
        $result = $mode->query($this->openId);
        return $result;
    }

    private function checkArgs()
    {
        $result = '';
        if (empty($this->openId)) {
            $result .= 'open id is null';
        }

        if (empty($result)) {
            return 'ok';
        } else {
            return $result;
        }
    }
}