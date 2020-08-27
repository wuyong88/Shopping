<?php
namespace app\admin\model;

use think\Model;

class Order extends Model
{	
	//获取器  get字段Attr
	//字段order_status
	//获取器的参数是当前对应数据的值
	// public function getOrderStatusAttr($value){
	 
	// 	$arr = [
	// 			0=>'未付款',  灰色
	// 		 	1=>'已付款',  绿色
	// 		 	2=>'未发货',  灰色
	// 		 	3=>'已发货',  绿色
	// 		 	4=>'进行中',  绿色
	// 		 	5=>'取消',    橙色
	// 		 	6=>'待取消',  绿色
	// 		 	7=>'已完成'   绿色
	// 		 ];
	// 	return $arr[$value];
	// }


	public static function getOrderList(){
	 
		return self::paginate(4);
	}

	public static function setOrderStatus($id, $status){
	 
		return self::where([
			'id' => $id
		])->update(['order_status' => $status]);
	}
}