<?php 
namespace app\home\model;

use think\Model;
//绝对公共的模型文件  
//目的: 前后台都能用
//位置: app/common/model/Category.php

class Menu extends Model
{
	public static function getMoreList(){
		$data = self::all();
		$arr = [];
		foreach ($data as $k => $v) {
			if(!$v['pid']){
				$arr[$k]['name'] = $v['name'];
				$arr[$k]['id'] = $v['id'] ;
				$arr[$k]['pid'] = $v['pid'];

				$arr[$k]['child'] = [];
			}
		}
 
 
		foreach ($data as $k => $v) {
			foreach ($arr as $key => $value) {
				if($v['pid'] == $value['id']){
					 
					$arr[$key]['child'][$k]['name'] = $value['name'];

					$arr[$key]['child'][$k]['id'] = $value['id'];
					$arr[$key]['child'][$k]['pid'] = $value['pid']; 
				}
			}
			
			
		}

		return $arr;
	}


	public static function getMenuList(){
		$data = self::where('module', 'home')
		->order('sort')
		->select();
		return self::setListData($data);
	}

	public static function setListData($data){
		$arr = [];
		foreach($data as $k=>$v){
			$arr[$k]['name'] = $v['name'];
			$arr[$k]['sort'] = $v['sort'];
			$arr[$k]['url'] = url($v['controller'].'/'.$v['action']);
		}
		return $arr;
	}
}