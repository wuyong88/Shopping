<?php 
namespace app\home\model;

use app\common\model\Category as CategoryBase;
//绝对公共的模型文件  
//目的: 前后台都能用
//位置: app/common/model/Category.php

class Category extends CategoryBase
{
	public static function list(){
		return self::getList(0);
	}

	public static  function getMenuList(){
		$data = self::where('is_menu', 1)->order('sort')->select();
		return self::setListData($data);
	}

	public static function setListData($data){
		$arr = [];
		foreach($data as $k=>$v){
			$arr[$k]['name'] = $v['name'];
			$arr[$k]['sort'] = $v['sort'];
			$arr[$k]['url'] = url('goods/index', ['cate_id'=>$v['id']]);
		}
		return $arr;
	}

	public static function getIndexData(){
		return self::where('is_index', 1)->select()->toArray();
	}
}