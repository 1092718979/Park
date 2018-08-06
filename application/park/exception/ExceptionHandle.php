<?php
/**
 * Created by JiFeng.
 * User: 10927
 * Date: 2018/5/13
 * Time: 9:58
 */

namespace app\park\exception;

use Exception;
use think\exception\Handle;
use think\Log;

class ExceptionHandle extends Handle{

    private $code;
    private $result;

    public function render(Exception $e){
        if ($e instanceof BaseExcetion){
            $this->code = $e->code;
            $this->result = $e->result;
        }else{
            if (config('app_debug')){
                return parent::render($e);
            }else{
                $this->code = 1000;
                $this->result = '服务器内部错误';
                Log::info([
                    'type'  => 'File',
                    'path'  => LOG_PATH,
                    'level' => ['error'],
                ]);
                Log::record($e->getMessage(),'error');
            }
        }
        $result = [
            'code' => $this->code,
            'result' => $this->result,
        ];
        return json($result,400);
    }

}
