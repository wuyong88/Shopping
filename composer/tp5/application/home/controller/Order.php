<?php
namespace app\home\controller;

use think\Controller;
use think\facade\Request;
use think\captcha\Captcha;
use app\home\model\User;
use app\home\model\Cart;
use think\facade\Session;
use think\facade\Cookie;
use think\facade\Order as OrderModel;
use think\facade\OrderGoods;
use think\Db;


/*
流程： 
	1. 用户添加购物车 -- 用户不登录可以添加【存cookie，目的减轻服务器压力】
	2. 用户中途登录，cookie存表【目的：长久保存】
	3. 提交订单
		登录 ，出现订单确认界面，选择收货地址
		不登录，显示登录界面
	3.5 确认提交支付，生成订单，存订单表，状态是未支付
	4. 支付，调用支付宝

*/
class Order extends Controller 
{
	public function confirm(){

		if(Request::isAjax()){
			if(!Session::has('user_info_home')){
				return json([
					'code' => 100,
					'msg' => '请登录',
					'url' => url('login/login')
				]);
			}

			$data = Request::post('index');
			// dump($data);die;
 
			return json([
					'code' => 200,
					'url' => url('Order/ConfirmOrder', ['id'=>$data])
				]);

		}
		//确认订单没有问题
		//支付
		//调用pay
	}

	public function ConfirmOrder(){
		$id = Request::param('id');
		$arr = explode('_', $id);
		$data = Cart::where(['id'=>$arr])->select();
       // dump($data);die;
		$addr = Db::name('address')->where([
			'user_id' => Session::get('user_info_home.id')
		])->select();
      // dump($addr);die;
		$this->assign('data', $data);
		$this->assign('id', $id);
		$this->assign('addr', $addr);
		return $this->fetch('cart/ConfirmOrder');
	}

	public function getOrderData($addr_id){
		return [
			  'order_status' => 0,
			  'order_no' => 'tp'.time(),
  			  'pay_time' => 0,
			  'delivery_time'=>0,
			  'receipt_time'=>0,
			  'user_id'=>Session::get('user_info_home.id'),
			  'user_addr_id'=>$addr_id,
			  'create_time'=>time(),
			  'update_time'=>time()
		];
	}

	public function getOrderGoodsData($order_id, $cart){
		return [
			  'order_id' => $order_id,
			  'goods_id' => $cart['goods_id'],
  			  'user_id' => Session::get('user_info_home.id'),
			  'goods_name'=> $cart['goods_name'],
			  'goods_spec_id'=> $cart['spec_key'],
			  'goods_attr'=>$cart['spec_key_name'],
			  'content'=>'',
			  'goods_no'=>'',
			  'goods_price'=>$cart['goods_price'],
			  'goods_weight'=>'',
			  'total_num'=>$cart['goods_num'],
			  'total_price'=>$cart['goods_price'] * $cart['goods_num'],
			  'create_time'=>time(),
		];
	}

	//订单第一次生成 状态是未支付
	public function orderCreate(){
		// Array ( 
		// 	[id] => 3_6_7 
		// 	[addr_id] => 1 
		// 	[total] => 22885 )

		$order = $this->getOrderData(Request::post('addr_id'));
		$order_id = Db::name('order')->insertGetId($order);
    
		

		$arr_id = explode('_', Request::post('id'));
		
		$order_goods = [];
		$cart = Db::name('cart')->where(['id'=>$arr_id])->select();
		
		foreach ($cart as $k => $v) {
			$order_goods[] = $this->getOrderGoodsData($order_id, $v);
		}
		
		

		//1. 存表 order  状态是未支付
		Db::name('order_goods')->insertAll($order_goods);
       // dump($order_goods);die;
		//接受数据
		$this->assign('order_no', $order['order_no']);
		$this->assign('data', Request::post());
		//2. 加载视图
		return $this->fetch('order/payment');

	}
	
}
