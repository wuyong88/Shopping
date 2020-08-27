<?php
namespace app\home\controller;

use think\Controller;
use think\facade\Request;
use think\captcha\Captcha;
use app\home\model\User;
use app\home\model\Cart;
use think\facade\Session;
use think\facade\Cookie;


class Login extends Controller 
{
	//登录
	public function login(){
		return $this->fetch('user/login');
	}

	public function logValidate(){
		$data = Request::post();
	 
		$validate = new \app\home\validate\User;
		if (!$validate->scene('login')->check($data)) {
            //有问题提示
            return json([
            	'status'=>100, 
            	'msg'=>$validate->getError()
           	]);
        }
 
 
        //没问题添加数据库
        $_data['mobile'] = $data['username'];
        $_data['password'] = $data['password'];
        $res = User::find($_data);
 

        //添加有问题提示
        if(!$res){
        	return json([
        		'status'=>101, 
            	'msg'=>'没有信息'
            ]);
        }

         //存session，直接显示登陆后的状态
		Session::set('user_info_home',$res);



		//将数据保留到购物车表 开始
			//数据在cookie中
			//数据在表中
			Cart::batchInsertCart(Cookie::get('cart'));
		//结束

        return json([
        	'status'=>200, 
            'msg'  =>'登陆成功',
            'url'  => url('index/index')
            ]);

	}

	//数据验证
	/*
	 验证器 用php实现的数据校验
		使用：
			1. mvc层新建validate文件夹
				里面新建验证器文件
			2. 验证器文件内容直接拷贝手册
				修改rule属性数组的下标为表单name的值
				右侧改为验证规则
			3. 控制器new验证器
				使用check(要验证的数据，数组)方法
			4. 验证器的getError() 错误消息
	*/
	public function resValidate(){
		$data = Request::post();
	 
		$validate = new \app\home\validate\User;
		if (!$validate->check($data)) {
            //有问题提示
            return json([
            	'status'=>100, 
            	'msg'=>$validate->getError()
           	]);
        }

        //没问题添加数据库
        $_data['mobile'] = $data['username'];
        $_data['password'] = $data['password'];
        $res = User::insert($_data);

       

        //添加有问题提示
        if(!$res){
        	return json([
        		'status'=>101, 
            	'msg'=>'注册失败'
            ]);
        }

         //存session，直接显示登陆后的状态
		Session::set('user_info_home',$_data);

        return json([
        	'status'=>200, 
            'msg'  =>'注册成功',
            'url'  => url('index/index')
            ]);

	}

	//注册
	public function regsiter(){
		return $this->fetch('user/regsiter');
	}

	//登出
	public function loginOut(){
		Session::delete('user_info_home');
		return redirect('index/index');
	}

	//验证码的应用
	/*
		1. composer下载类库  cmd在项目根目录运行
			composer require topthink/think-captcha=2.0.*
		2. html使用 <img src="{:captcha_src()}"/>
		3. 验证器
			'验证码name值' => 'require|captcha'

	*/
	//自定义验证码，以及调用
	public function code(){
		$config =    [
		    'imageH'    =>    30,    
		    'imageW'    =>    100,   
		];
		$captcha = new Captcha($config);
		return $captcha->entry();
	}
}
