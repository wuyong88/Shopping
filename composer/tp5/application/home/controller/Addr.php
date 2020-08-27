<?php
namespace app\home\controller;

use think\Controller;
use think\facade\Request;
use think\captcha\Captcha;
use app\home\model\User;
use app\home\model\Cart;
use think\facade\Session;
use think\facade\Cookie;
use think\Db;


class Addr extends Controller 
{
	 public function add(){
	 	$province = $this->getProvince();
 
	 	$this->assign('province', $province);
	 	return $this->fetch('addr/address');
	 }
 /*
   省份
 */
	 public function getProvince(){
	 	return Db::name('city')
	 	->where([
	 		'type' => 1
	 	])->select();
	 }

 /*
   城市
 */
	 public function getCity($id){
	 	$city = Db::name('city')
	 	->where([
	 		'type' => 2,
	 		'parent_id' => $id
	 	])->select();
        // 交互状态 要用json格式
	 	return json([
	 		'status' => 200,
	 		'msg'    => '获取成功',
	 		'data'   => $city
	 	]);
	 }

 /*
   区域
 */
	 public function getDistrict($id){
	 	$district = Db::name('city')
	 	->where([
	 		'type' => 3,
	 		'parent_id' => $id
	 	])->select();

	 	return json([
	 		'status' => 200,
	 		'msg'    => '获取成功',
	 		'data'   => $district
	 	]);
	 }

 /*
   收货地址提交
 */
	 public function getAddr(){
	 	$data = Request::post();
	 	// dump($data);die;
	 	$res=Db::name('address')->insert($data);
	 	// dump($res);die;
	 	if($res){
	 	return json([
	 		'status' => 200,
	 		'msg'    => '获取成功',
	 		'url'   => url("cart/shopping/")
	 	]);

	 	}else{
	 		return false;
	 	}
	 }

}
