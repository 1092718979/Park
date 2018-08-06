<?php
/**
 * Created by PhpStorm.
 * User: Bruce
 * Date: 2018/5/18
 * Time: 17:22
 * 用户投诉控制类
 * $mainKey,$openId,$parkId,$spaceId,$cause,$context
 */

namespace app\park\controller;

use think\Request;
use app\park\model\ComplaintMode;

class Complaint
{
    private $imagePath;//图片的绝对路径（根目录/image/yyyy/mm/dd）
    private $savePath;//保存在数据库中的路径
    private $mode;//model指针
    private $complaintId;//投诉id
    private $openId;
    private $parkId;
    private $spaceId;
    private $cause;//投诉原因
    private $context;//投诉内容

    private $cId;

    public function index(Request $request)
    {

        $this->savePath = date('Y') . DS . date('m') . DS . date('d') . DS;//yyyy/mm/dd  格式目录
        $this->imagePath = ROOT_PATH . 'images' . DS . $this->savePath;
        $this->complaintId = time() . random_int(1000, 9999);
        $this->mode = new ComplaintMode();
        $result = array();

        $this->cId = $request->post('complaintId');
        if ($this->cId != "") {//数据库中已经有投诉id（不是递归上第一次），只需要保存图片
            $this->complaintId = $this->cId;
            $image = $this->upLoad('file', $request, $this->mode);
            $result['code'] = '200';
            $result['image'] = $image;
            return $result;
        }

        $this->openId = $request->post('openId');
        $this->parkId = $request->post('parkId');
        $this->spaceId = $request->post('spaceId');
        $this->cause = $request->post('cause');
        $this->context = $request->post('context');
        if (($msg = $this->checkArgs()) != 'ok') {
            return ['code' => '300', 'result' => $msg];
        }


        $result['insertText'] = $this->mode->insertText($this->complaintId, $this->openId, $this->parkId, $this->spaceId,
            $this->cause, $this->context);
        $image = $this->upLoad('file', $request, $this->mode);
        $this->mode = null;

        $result['code'] = '200';
        $result['image'] = $image;
        $result['complaintId'] = $this->complaintId;
        return $result;
    }

    private function checkArgs()
    {
        if ($this->openId == null) {
            return 'openId is null';
        }
        if ($this->parkId == null) {
            return 'parkId is null';
        }
        if ($this->spaceId == null || strlen($this->spaceId) > 20) {
            $this->spaceId = '';
        }
        if ($this->cause == null || mb_strlen($this->cause, 'UTF-8') > 10) {
            return 'cause is error';
        }
        if ($this->context == null || mb_strlen($this->context, 'UTF-8') > 50) {
            return 'context is error';
        }
        return 'ok';
    }

    private function upLoad($fileName, Request $request, ComplaintMode $mode)
    {
        $path = str_replace('\\', '/', $this->savePath);// 把‘\’替换为'/'兼容windows和linux
        $images = $request->file($fileName);

        if (!is_dir($this->imagePath)) {
            mkdir($this->imagePath, 0777, true);
        }
        if ($images == null) {
            return 0;
        }
        $result = '';
        if (is_array($images)) {//是否是多图上传
            foreach ($images as $i) {
                $saveName = time() . random_int(1000, 9999);//保存的文件名，时间戳+4位随机数
                $info = $i->move($this->imagePath, $saveName);//文件移动到指定目录，根据saveName保存
                if ($info) {
                    //拼接文件名和拓展名
                    $e = explode('.', $images->getInfo('name'));
                    $result .= $mode->insertPath($path . $saveName . '.' . $e[count($e) - 1], $this->complaintId);
                }
            }
            return $result;
        } else {
            $saveName = time() . random_int(1000, 9999);
            $info = $images->move($this->imagePath, $saveName);
            if ($info) {
                $e = explode('.', $images->getInfo('name'));
                $result .= $mode->insertPath($path . $saveName . '.' . $e[count($e) - 1], $this->complaintId);
                return $result;
            }
        }

        return 'insert image error';
    }
}