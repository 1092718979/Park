<?php
/**
 * Created by PhpStorm.
 * User: Bruce
 * Date: 2018/7/18
 * Time: 14:55
 */

namespace app\park\controller;

use app\park\model\LoginMode;
use think\Request;

class Login
{
	private $js_code;
	private $appId = "wxdf08ee2795813cc1";
	private $secret = "d889304d0ed055cfb59c078f078313fb";
	//    private $grant_type = "authorization_code";
	private $openId;
	private $sessionKey;
	private $url;

	public function index(Request $request)
	{
		$this->js_code = $request->post("js_code");
		if ($this->js_code == "")
			return ['code' => '300', 'result' => 'js_code is null'];
		else {
			$this->url = "https://api.weixin.qq.com/sns/jscode2session?appid=$this->appId&secret=$this->secret&js_code=$this->js_code&grant_type=authorization_code";

			$curl = curl_init(); // 启动一个CURL会话
			curl_setopt($curl, CURLOPT_URL, $this->url);
			curl_setopt($curl, CURLOPT_HEADER, false);
			curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
			curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, 0); // 跳过证书检查
			curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, 0);  // 从证书中检查SSL加密算法是否存在

			curl_setopt($curl, CURLOPT_CONNECTTIMEOUT, 10);
			$result = curl_exec($curl);     //返回api的json对象
			//关闭URL请求
			curl_close($curl);

			//            $result = "{\"session_key\":\"dr0MtbJQ6tQtJAKBXfVcmQ==\",\"openid\":\"ovgcr5FrKhzvTX7RibJEeDiqhwVo\"}";
			$data = json_decode($result);
			$this->openId = $data->openid;
			$this->sessionKey = $data->session_key;
			$mode = new LoginMode();
			return $mode->insert($this->openId);
		}

	}
}
