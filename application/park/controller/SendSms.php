<?php
/**
 * Created by PhpStorm.
 * User: Bruce
 * Date: 2018/7/18
 * Time: 11:25
 *
 * 发送短信 ｛phoneNumber：手机号   code验证码（6位）}
 */

namespace app\park\controller;

use app\park\sms\SignatureHelper;
use think\Request;

class SendSms
{
    private $phoneNumber;
    private $code;

    public function index(Request $request)
    {
        $this->phoneNumber = $request->post("phoneNumber");
        $this->code = $request->post("code");
        if (!$this->checkout()) {
            return ['code' => '300', 'result' => 'phone or code error'];
        }
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
        $params["TemplateCode"] = "SMS_139885195";

        // fixme 可选: 设置模板参数, 假如模板中存在变量需要替换则为必填项
        $params['TemplateParam'] = Array(
            "code" => $this->code,
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

    private function checkout()
    {
        if (strlen($this->code) != 6 || strlen($this->phoneNumber) != 11)
            return false;
        else
            return true;
    }
}