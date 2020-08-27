<?php 
namespace app\admin\controller;

use app\admin\common\Common; 
use app\admin\model\Category as CategoryModel;
use think\facade\Request;

class Category extends Common
{
	public function index(){
		//非静态方法的调用
		//$c = new CategoryModel;
		//dump($c->add());
		//模型的对象->方法名()

		//静态方法的调用
		//模型名::方法名()

		//1. 模型查询数据---分类树
		//2. 列表页显示
			//a. 所有的跳转地址【增删改】修改正确，参考商品html


		$list = CategoryModel::list();
 
		$this->assign('list', $list);
		return $this->fetch();
	}

	public function edit(){
		$id = Request::param('id');
		$list = CategoryModel::list();
 		$data = CategoryModel::findData($id);

 		$this->assign('id', $id);
		$this->assign('list', $list);
		$this->assign('data', $data);
		return $this->fetch();
	}

	public function editData(){
		$data = Request::param();
	 
		//修改
		$res = CategoryModel::edit($data['id'], $data['category']);
		if($res){
			//成功
			return json(array_merge(config('data.success'),['url'=>url('index')]));
		}
		//失败
		return json(config('data.error'));
	}

	public function add(){
		$list = CategoryModel::list();
 
		$this->assign('list', $list);
		return $this->fetch();
	}

	public function addData(){
		$data = Request::post('category');
		 
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
		$id = Request::post('category_id');

		$res = CategoryModel::del($id);
		if($res == -1){
			return json(config('data.sub_error'));
		}else if($res){
			return json(array_merge(config('data.success'),['url'=>url('index')]));
		}
	    return json(config('data.error'));
	}
}