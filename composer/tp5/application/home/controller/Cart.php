<?php
namespace app\home\controller;

use think\Controller;
use think\Db;
use think\facade\Request;
use think\captcha\Captcha;
use app\home\model\User;
use app\home\model\Goods;
use app\home\model\Cart as CartModel;
use app\home\model\GoodsSpec;
use think\facade\Session;
use think\facade\Cookie;


class Cart extends Controller 
{	
	/*
		产品详情页显示商品，点击加入购物车
		1. 判断库存，有了加
		2. 秒杀，判断库存来不及  一秒钟流量激增 
		3. 购物车商品一直做  关系型数据库
		4. 购物车cookie 

		减少服务器压力为主 web服务器  数据库服务器
	

		加入数据到cookie--目的：减轻服务器压力，用户不同登陆
		提交购物车数据，用户登录 -> 数据到服务器 关系型数据库存储数据    目的：达到长期保存的效果
		新打开购物车界面，购物车数据缓存    目的：尽量少操作数据库
		
		支付后删除对应购物车数据
	*/
     //1.将购车信息放进属性中
	 protected $car_data;


	 public function list(){
	      //登录时候用
	 	// CartModel::BatchInsertCart(Cookie::get('cart'));
	 	 if(Session::get('user_info_home')){

	 	 	$cart_data = CartModel::getCartData(Session::get('user_info_home.id'));
	 	 }else{
	 	 	$cart_data = Cookie::get('cart');
	 	 	// dump($cart_data);die;
	 	 	foreach ($cart_data as $k => $v) {
	 	 	   //补全购物车商品信息
	 	 		$final_cart_data[] = array_merge($this->getCartInsertData($v), $v);
	 	 	}
            // dump($final_cart_data);die; 
	 	  	$cart_data = $final_cart_data;
	 	 }


 
	 	 Cookie::set('cart', $cart_data);
         // 2.将购车信息放进属性中
	 	 $this->assign('cart_data', $cart_data);

	 	 return  $this->fetch('cart/shopping');
	 
	 }

	 public function edit(){}

	 public function getCartInsertData($data){

	 	return [
	 		'user_id'  => Session::get('user_info_home.id'),
  			'goods_id' => $data['id'],
  			'goods_name' => Goods::getDetailData($data['id'])['goods_name'],
  			'goods_price' => GoodsSpec::getCartSpcePrice($data['spec_rel_id'])['goods_price'],   //只要商品里面的价格
  			'goods_num' => $data['num'],
  			'spec_key'  => $data['spec_rel_id'],
  			'spec_key_name' => $data['spec'],
  			'selected' => 1,
  			'add_time' => time()];
	 }


	 public function add(){

	 	$data = Request::post();
	 	// dump($data);die;
	   	$is_rep = false;
 
	 	if(Session::get('user_info_home')){
  
	 		$data = $this->getCartInsertData($data);
           // dump($data);die;
	 		CartModel::insertCart($data);
	 		return redirect('list');
	 	}

      // Cookie::delete('cart');die; //清除Cookie
	 	if(!Cookie::has('cart')){
	 		$cookie = [$data];
	 	}else{

	 		$cookie = Cookie::get('cart');
	 	
	 		// 购物车里面的id如果和提交数据的id相等并且规格相等的时候偶
	 		foreach ($cookie as $k => $v) {
	 			if($v['id'] == $data['id'] && $v['spec'] == $data['spec']){
	 				$cookie[$k]['num']++;//就让商品数量加一
	 				$is_rep = true; //表示商品已经累加过了
	 			}
	 		}

	 		if(!$is_rep){
	 			$cookie[] = $data;
	 		}
	 	}
         
	 	Cookie::set('cart', $cookie);//永久保存

	 	return redirect('list');//重定向 
	 	 
	 }


	 public function del(){}

	 public function unSelected(){
	 	$cookie = Cookie::get('cart');
	 	$cookie[Request::post('index')]['selected'] = Request::post('value');
		Cookie::set('cart', $cookie);
	 }
 /*
 删除购物车数据
 */
	 public function unCart(){
	 	//1. 获取购物车
	 	$cookie = Cookie::get('cart');
	 	//2. 取到当前购物车数据 删掉
	 	unset($cookie[Request::post('index')]);
         // 3.再还给购物车数据
		Cookie::set('cart', $cookie);
	 }
}
 