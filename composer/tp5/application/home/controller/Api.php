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


class Api extends Controller 
{
	public function getGoods(){
		//关闭错误信息
		error_reporting(0);

		//业务操作
		$data = Db::name('goods')
		->select();

		//回调数据
		return json([
			'status' => 200,
			'msg'    => '请求成功',
			'data'   => $data
		]);
	}
}
