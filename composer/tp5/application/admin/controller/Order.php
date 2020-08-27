<?php
namespace app\admin\controller;

use app\admin\common\Common;
use think\Db;
use think\facade\Request; 
use app\admin\model\Order as OrderModel;



/**
* 规格控制器
*/
class Order extends Common
{ 
	public function index(){
		$data = OrderModel::getOrderList();

		$this->assign('data', $data);
		return $this->fetch('order_list');
	}

	public function setOrderStatus(){
		$res= OrderModel::setOrderStatus(Request::post('id'), 3);
		if($res){
			return json([
				'status' => 200,
				'msg'    => '发货成功'
			]);
		}else{
			return json([
				'status' => 100,
				'msg'    => '发货失败'
			]);
		}
	}
 
	public function detail(){	
		 $id = Request::param('id');
		 // dump($id);
		 //当前订单信息
		 $order = Db::name('order')->where(['id'=>$id])->find();

		 //当前订单内的所有商品
		 $order_goods = Db::name('order_goods')->where(['order_id'=>$id])->select();

		 //用户的信息   创建用户地址表
		 Db::name('user_addr')->where(['id' => $order['user_id']])->find();

		 return $this->fetch('order_details');
	}
}
