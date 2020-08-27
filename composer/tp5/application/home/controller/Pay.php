<?php
namespace app\home\controller;

use think\Controller;
use think\facade\Request;
use think\captcha\Captcha;
use app\home\model\User;
use app\home\model\Cart;
use think\facade\Session;
use think\facade\Cookie;
use alipay\PayAction;

/*
  支付的步骤
   1.更改文件名(index改为PayAction)
   2. 加命名空间 PayAction里面加namespace alipay
     submit里面加namespace alipay;
   3.将alipay复制到extend目录下
   4.config里面需要更改的有
     (1)appid (合作身份者ID)
     (2)秘钥进行处理更换 
     (3)服务器异步跳转地址
     (4)页面跳转同步地址
*/


class Pay extends Controller 
{   
	// 赋一个属性
	protected  $alipay;

	//展示页面  案例
	public function pay(){
		 return $this->fetch('pay/index');
	}

	//提交->准备调用支付宝
	public function payOrder(){
		$data = Request::post();
        // dump($data);die;
		$this->alipay = new PayAction;
		$this->alipay->alipay();

	}
    // 异步跳转方法
	public function notify_url(){
		// 调用alipay里面的异步跳转方法
		$this->alipay->notify_url();
	}
      // 同步跳转方法
	public function return_url(){
		$this->alipay = new PayAction;
		// 调用alipay里面的同步跳转方法
		$this->alipay->return_url();
	}
}
