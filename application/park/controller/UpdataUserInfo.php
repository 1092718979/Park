<?php
/**
 * Created by PhpStorm.
 * User: Bruce
 * Date: 2018/4/29
 * Time: 9:57
 * 上传及更新用户信息{openId,session}
 *返回{stat:success/fail,message:''}
 */

namespace app\park\controller;

use think\Request;
use app\park\model\UpdateUserInfoMode;

class UpdataUserInfo
{

    private $openId;
    private $userName;
    private $sessionKey;

    public function index(Request $request)
    {
        $this->openId = $request->post('openId');
        $this->userName = $request->post('userName');
        $this->sessionKey = $request->post('sessionKey');
        if ($this->openId == null) {
            $response = ['code' => '300', 'result' => 'openId is null'];
            return $response;
        }
        $mode = new UpdateUserInfoMode();
        return ($mode->update($this->openId, $this->userName, $this->sessionKey));
    }
}