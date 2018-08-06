<?php
namespace app\park\controller;

use \think\Request;

/**
 * Created by PhpStorm.
 * User: Bruce
 * Date: 2018/4/29
 * Time: 9:28
 */
class Park
{
    public function park(Request $request)
    {
        $images = $request->file('file');
        if ($images != "") {
            echo "ok";
        } else {
            echo "error";
        }
    }
}