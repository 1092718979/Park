<?php
/**
 * Created by PhpStorm.
 * User: Bruce
 * Date: 2018/5/18
 * Time: 22:38
 */

namespace app\park\model;

use think\Db;

class ComplaintMode
{
    private $db;

    public function __construct()
    {
        $this->db = Db::connect();
    }

    public function insertText($mainKey, $openId, $parkId, $spaceId, $cause, $context)
    {
        if ($spaceId == null) {
            $data = ['complaint_id' => $mainKey, 'open_id' => $openId, 'park_id' => $parkId,
                'cause' => $cause, 'context' => $context];
        } else {
            $data = ['complaint_id' => $mainKey, 'open_id' => $openId, 'park_id' => $parkId,
                'space_id' => $spaceId, 'cause' => $cause, 'context' => $context];
        }
        $rus = $this->db->table('complaint')->insert($data);
        if ($rus == 1) {
            return 'success';
        } else {
            return 'fail';
        }
    }

    public function insertPath($path, $complaint_id)
    {
        $data = ['complaint_id' => $complaint_id, 'path' => $path];
        $rus = $this->db->table('image_path')->insert($data);
        if ($rus == 1) {
            return 'success';
        } else {
            return 'fail';
        }
    }

    function __destruct()
    {
        // TODO: Implement __destruct() method.
        if ($this->db != null) {
            $this->db->close();
        }
    }
}