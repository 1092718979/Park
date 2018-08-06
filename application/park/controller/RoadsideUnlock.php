<?php
/**
 * Created by PhpStorm.
 * User: Bruce
 * Date: 2018/4/29
 * Time: 10:13
 * 9.路边开锁{openId,parkId,space,,orderId}
 * 返回{stat:'success/fail',message:''}
 */

namespace app\park\controller;

use app\park\model\RoadsideUnlockMode;
use app\park\sms\SignatureHelper;
use think\Request;

class RoadsideUnlock
{

    private $openId;
    private $parkId;
    private $spaceId;
    private $billId;//订单号

    private $phoneNumber;
    private $code;

    public function index(Request $request)
    {
        $this->openId = $request->post('openId');
        $this->parkId = $request->post('parkId');
        $this->spaceId = $request->post('spaceId');
        $this->billId = $request->post('billId');
        $this->code = '1111';

        $result = $this->checkArgs();
        if ($result != 'ok') {
            return ['code' => '300',
                'result' => $result];
        }
        //参数正确，可以开始
        // 1 给车位锁发指令开锁

        $mode = new RoadsideUnlockMode();
        $this->spaceId = $this->parkId . '_' . $this->spaceId;
        $this->phoneNumber = $mode->getPhoneNumber($this->parkId, $this->spaceId);//获取手机号
        if ($this->phoneNumber != 'number is null') {
            if ($this->open()) {//发短信开锁
                return ['code' => 200, 'result' => 'open success'];
            }
        } else {
            return ['code' => 200, 'result' => 'not find number'];
        }

        // 2修改数据库中订单的状态
        $res = $mode->changeState($this->billId);
        if ($res) {
            return ['code' => '200', 'result' => 'unlock ok'];
        } else {
            return ['code' => '200', 'result' => 'unlock fail'];
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
            return $result;
        }
        if (empty($this->billId)) {
            $result .= 'billId is null';
        }

        if (empty($result)) {
            return 'ok';
        } else {
            return $result;
        }
    }

    private function open()
    {
        $params = array();
        // *** 需用户填写部分 ***
//        echo "send start";
        // fixme 必填: 请参阅 https://ak-console.aliyun.com/ 取得您的AK信息
        $accessKeyId = "LTAIhGIxrBytgAPG";
        $accessKeySecret = "JLfVXsd9oDehHBJ4Mj6ds52tFm4MPf";

        // fixme 必填: 短信接收号码
        $params["PhoneNumbers"] = $this->phoneNumber;

        // fixme 必填: 短信签名，应严格按"签名名称"填写，请参考: https://dysms.console.aliyun.com/dysms.htm#/develop/sign
        $params["SignName"] = "李谦祥";

        // fixme 必填: 短信模板Code，应严格按"模板CODE"填写, 请参考: https://dysms.console.aliyun.com/dysms.htm#/develop/template
        $params["TemplateCode"] = "SMS_140726601";

        // fixme 可选: 设置模板参数, 假如模板中存在变量需要替换则为必填项
        $params['TemplateParam'] = Array(
            "name" => "open",
            "number" => $this->code
//            "product" => "阿里通信"
        );

        // fixme 可选: 设置发送短信流水号
//        $params['OutId'] = "12345";

        // fixme 可选: 上行短信扩展码, 扩展码字段控制在7位或以下，无特殊需求用户请忽略此字段
//        $params['SmsUpExtendCode'] = "1234567";


        // *** 需用户填写部分结束, 以下代码若无必要无需更改 ***
        if (!empty($params["TemplateParam"]) && is_array($params["TemplateParam"])) {
            $params["TemplateParam"] = json_encode($params["TemplateParam"], JSON_UNESCAPED_UNICODE);
        }

// 初始化SignatureHelper实例用于设置参数，签名以及发送请求
        $helper = new SignatureHelper();

// 此处可能会抛出异常，注意catch
        $content = $helper->request(
            $accessKeyId,
            $accessKeySecret,
            "dysmsapi.aliyuncs.com",
            array_merge($params, array(
                "RegionId" => "cn-hangzhou",
                "Action" => "SendSms",
                "Version" => "2017-05-25",
            ))
// fixme 选填: 启用https
// ,true
        );
        return $content;
    }
}