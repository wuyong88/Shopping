<?php 
namespace app\admin\model;
use think\Model;
//app/admin/model/Category.php
class Category extends Model
{
	//重新设置模型对应的表名
	protected $table = 'tp_category';

	//重新设置表的主键[id一定是表内真是存在的]
	protected $pk = 'id';

	//调用   模型名::list()
	public static function list($pid=0, $arr=[], $level=0, $name='顶级分类'){
		//当前类对象      //$this->  
		//当前类本身      //self::

		$data = self::where(['parent_id'=>$pid])->select();

		foreach($data as $k=>$v){
			$arr[$v['id']]['id'] = $v['id'];
			$arr[$v['id']]['name'] = str_repeat('|--', $level).$v['name'];
			$arr[$v['id']]['parent_name'] = $name;
			$arr[$v['id']]['create_time'] = $v['create_time'];
			$arr[$v['id']]['sort'] = $v['sort'];

			$arr = self::list($v['id'], $arr, $level+1, $v['name']);
		}
		
		return $arr;
	}

	//获取数据个数
	public static function getCount($id){
		return self::where(['parent_id'=>$id])->count();
	}

	//删除数据
	public static function del($id){
		//删除之前，根据当前id，查询pid是否有内容，、
		//如果有怎么返回 -1 控制器根据-1做提示，不能删除
		if(self::getCount($id)){
			return -1;
		}
		return self::where(['id'=>$id])->delete();
	} 

	//添加
	public static function add($data){
		return self::create($data);
	}

	//修改
	public static function edit($id, $data){

		return self::where(['id' => $id])->update($data);
	} 

	//查询
	public static function findData($id){
		return self::where(['id' => $id])->find();
	} 
}