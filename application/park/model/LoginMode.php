<?php
/**
 * Created by PhpStorm.
 * User: Bruce
 * Date: 2018/7/18
 * Time: 17:35
 */

namespace app\park\model;

use think\Db;

class LoginMode
{
	private $db;

	public function __construct()
	{
		$this->db = Db::connect();
	}

	public function insert($openId)
	{
		$count = $this->db->table('user_info')->where('open_id', $openId)->count();
		if ($count == 0) {
			$values = ['open_id' => $openId];
			$res = $this->db->table("user_info")->insert($values);
			if ($res == 1) {
				return ['open_id' => $openId, 'code' => '200', 'result' => 'success'];
			} else {
				return ['open_id' => $openId, 'code' => '200', 'result' => 'fail'];
			}
		} else {
			$result = $this->db->table('user_info')->field('open_id,phone_number,car_id')->where('open_id', $openId)->find();
			$result['code'] = 200;
			return $result;
		}
	}

	public function __destruct()
	{
		// TODO: Implement __destruct() method.
		if ($this->db != null) {
			$this->db->close();
		}
	}
}
