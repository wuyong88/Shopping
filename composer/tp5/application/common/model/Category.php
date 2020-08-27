<?php 
namespace app\common\model;

use think\Model;
//绝对公共的模型文件  
//目的: 前后台都能用
//位置: app/common/model/Category.php

class Category extends Model
{
	public static function getListTree($type=1, $pid=0, $arr=[], $level=0, $name='顶级分类'){
		$data = self::where(['parent_id'=>$pid])->select();

		foreach($data as $k=>$v){
			$arr[$v['id']]['id'] = $v['id'];

			if($type){
				$arr[$v['id']]['name'] = str_repeat('|--', $level).$v['name'];
			}else{
				$arr[$v['id']]['name'] = $v['name'];
			}
			
			$arr[$v['id']]['parent_name'] = $name;
			$arr[$v['id']]['create_time'] = $v['create_time'];
			$arr[$v['id']]['sort'] = $v['sort'];

			$arr = self::getList($type, $v['id'], $arr, $level+1, $v['name']);
		}
		
		return $arr;
	}


	public static function getMoreList(){
		$data = self::all();
		$arr = [];
		foreach ($data as $k => $v) {
			if(!$v['parent_id']){
				$arr[$k]['name'] = $v['name'];
				$arr[$k]['id'] = $v['id'] ;
				$arr[$k]['parent_id'] = $v['parent_id'];

				$arr[$k]['child'] = [];
			}
		}
 
 
		foreach ($data as $k => $v) {
			foreach ($arr as $key => $value) {
				if($v['parent_id'] == $value['id']){
					 
					$arr[$key]['child'][$k]['name'] = $v['name'];

					$arr[$key]['child'][$k]['id'] = $v['id'];
					$arr[$key]['child'][$k]['parent_id'] = $v['parent_id']; 
				}
			}
			
			
		}
 
		return $arr;
	}


}