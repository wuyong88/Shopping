<?php 
namespace app\admin\model;
use think\Model;
//app/admin/model/Admin.php
class Admin extends Model
{

	//重新设置表的主键[id一定是表内真是存在的]
	protected $pk = 'id';


	//获取数据个数
	public static function getCount($id){
		return self::where(['id'=>$id])->count();
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

	//多条查询
	public static function list(){
		return self::where(['is_del' => 0])->select();
	} 
}