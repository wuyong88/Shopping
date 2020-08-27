<?php
namespace app\admin\common;

use think\Controller;
use think\facade\Session;
 
//common文件夹与controller文件夹同级
//common文件夹下新建Common.php
//表示是后台的公共文件夹
//Common类继承基类
//controller下面其他类继承Common类
class Common extends Controller{
	
	/**
    * 初始化方法 代替 构造方法
    */
    protected function initialize()
    {
        if( !Session::has('userinfo', 'admin') ){
            //模块 控制器 方法
            $this->redirect('admin/login');
        }

        //用户名获取,html显示
        $this->assign('username', Session::get('userinfo.user_name', 'admin'));
    }
}