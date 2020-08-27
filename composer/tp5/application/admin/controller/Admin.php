<?php
namespace app\admin\controller;

use think\Controller; //控制器基类
use think\facade\Request; //请求类
use think\Db; //数据库类
//use think\facade\Url;  url操作类
//use think\facade\Config; //操作配置文件的类
use think\facade\Session; //操作session类
use app\admin\model\Admin as AdminModel;


class Admin extends Controller
{

	/**
	* url操作
	*/
	function urlActive(){
		// string(44) "/tp5/public/index.php/admin/index/index.html"
		// string(44) "/tp5/public/index.php/index/index/index.html"
 		// 	string(69) "http://www.baidu.com/tp5/public/index.php/index/index/index/a/1.shtml"

		//助手函数
		//dump( url('index/index', ['a'=>1,'b'=>2], 'wh') ) ;

		//静态方法
		//dump( Url::build('index/index/index', ['a'=>1], 'shtml', 'www.baidu.com') );
	}

	function config(){
		//config 获取配置
		//参数 :  文件名. 获取当前文件内所有信息
		//参数 :  文件名.文件内数组下标 获取文件内一部分数据
		//dump( config('data.login.name_error') );

		//dump( Config::get('data.login.name_error') );
	}

	function merge(){
		// dump(  config('data.success')  );
		// dump(  ['url'=>'xxxxx/x/x/x/x/x/x'] );
		// dump(  array_merge(config('data.success'), ['url'=>'xxxxx/x/x/x/x/x/x']) );
		// die;
	}

	function session(){
		// $arr = ['name'=>'xxx', 'pwd'=>'asdsd'];
		// // Session::set('userinfo',$arr,'admin');

		// // dump(Session::get('userinfo', 'admin'));
		// // Session::delete('userinfo', 'admin');

		// // dump(Session::get('userinfo', 'admin'));


		// session('userinfo', $arr, 'admin');

		// dump(session('userinfo', '', 'admin'));

		// session('userinfo', null, 'admin');

		// dump(session('?userinfo', '', 'admin'));

		// die;
	}
	/**
	* 登录方法
	*/
	public function login(){
		
		return $this->fetch();
	}

	/**
	* 登录数据处理
	*/
	public function loginCheck(){
		

		//获取post数据
	 	$data = Request::post();

	 	if(!$data){
	 		//没数据
	 		return json(config('data.empty_data'));
	 	}

	 	$admin_data = Db::name('admin')
	 		->alias('a')
	 		->join('tp_admin_role ar', 'a.id=ar.admin_id', 'left')
	 		->join('tp_role r', 'ar.role_id=r.id', 'left')
	 		->where('a.username="'.$data['username'].'"')
	 		->find();

	 	if(!$admin_data){
	 		//账号不对
	 		return json(config('data.login.name_error'));
	 	}

	 	if($admin_data['password'] != md5($data['password'])){
	 		//密码不对
	 		return json(config('data.login.pwd_error'));
	 	}

	 	//其他页面要用到用户信息所以保存session
	 	Session::set('userinfo', $admin_data, 'admin');

	 	// 合并数组用 多个数组合成一个
	 	// array_merge(数组,数组,数组);
	 	//成功提示
	 	return json(array_merge(config('data.success'), ['url'=> url('index/index')]));
			//select a.*,r.name from tp_admin as a left join tp_admin_role  as ar on a.id=ar.admin_id left join tp_role as r on ar.role_id=r.id where a.username='admin';
	  
	 
	}
 
	public function loginOut(){
		Session::delete('userinfo', 'admin');

		//默认当前模块  默认当前控制器  只写方法名
		//填写完整的 模块/控制器/方法名
		//return redirect(url('/admin/admin/login'));
		return redirect('login');
	}



	public function index(){
		$list = AdminModel::list();

 		$this->assign('list', $list);
		return $this->fetch();
	}

	public function edit(){
		$id = Request::param('id');
 		$data = AdminModel::findData($id);

 		$this->assign('id', $id);
		$this->assign('data', $data);
		return $this->fetch();
	}

	public function editData(){
		$data = Request::param();
	 	//dump($data);die;
		//修改
		$res = CategoryModel::edit($data['id'], $data['admin']);
		if($res){
			//成功
			return json(array_merge(config('data.success'),['url'=>url('index')]));
		}
		//失败
		return json(config('data.error'));
	}

	public function add(){
		return $this->fetch();
	}

	public function addData(){
		$data = Request::post();

		//dump($data);die;
		 
		//添加
		$res = CategoryModel::add($data);
		if($res){
			//成功
			return json(array_merge(config('data.success'),['url'=>url('index')]));
		}
		//失败
		return json(config('data.error'));
	}

	public function del(){
		$id = Request::post('id');

		$res = AdminModel::del($id);
		if($res){
			return json(array_merge(config('data.success'),['url'=>url('index')]));
		}
	    return json(config('data.error'));
	}
}