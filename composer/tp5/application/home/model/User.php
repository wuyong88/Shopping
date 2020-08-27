<?php 
namespace app\home\model;

use think\Model;

class User extends Model
{
	//用户添加
	public static function insert($data){
		if(!$data){
			return false;
		}
		return self::create($data);
	}

	//用户查找
	public static function find($where){
		if(!$where){
			return false;
		}
		return self::where($where)
		->find();
	}
}