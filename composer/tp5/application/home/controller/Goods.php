<?php
namespace app\home\controller;

use think\Controller;
use think\facade\Request;
use think\captcha\Captcha;
use app\home\model\User;
use think\facade\Session;
use app\home\model\Goods as GoodsModel;
use Page\Page;
use app\home\model\GoodsSpec;


class Goods extends Controller 
{
	 
	public function index(){
 		
/* 
 分页
   分页的实现
   1.在extend里面新建文件page 里面写方法Page
   2.文件page 里面写方法Page Page.php
   3.分页方法用url调整 使用Request::param(); 获取参数
   4.调用控制引入命名空间 输出   
*/
		$data = $this->getGoods();
		//调用分页
		$page = Page::page($data->total(),$data->currentPage());

/*
 获取规格 在模型
 html输出 双重循环
 点击不同规格上坪变化
 */
		$spec_data = GoodsModel::getSpec();

		//规格需要的url参数
		$param = Request::param();

		
		
		if(isset($param['spec_value_id'])){
			unset($param['spec_value_id']);
		}
		
		if(Request::isAjax()){
			return json([
				'data' => $data,
				'page' => $page,
				'spec_data' => $spec_data,
				'param' => $param,
			]);
		}else{
			$this->assign('data', $data);
			$this->assign('page', $page);
			$this->assign('spec_data', $spec_data);
			//dump($spec_data);die;
			$this->assign('param', $param);
			return $this->fetch('pro/nanzhuang');
		}
	}

	public function getUrlParam(){
		$where = [];
		$data = Request::param();

		if(isset($data['cate_id'])){
			$where[] = ['category_id', '=', $data['cate_id']];
		}

		if(isset($data['parice'])){
			//$where[] = ['parice', '=', $data['parice']];
		}

		if(isset($data['count'])){
			//$where[] = ['count', '=', $data['count']];
		}

		 

		if(Request::isAjax()){
			$goods_id = GoodsSpec::getGoodsId($data['spec_value_id']);


			$data_id = GoodsModel::getCurCateGoods($data['cate_id'], $goods_id);
			// dump($data);die;

			$where[] = ['id', 'in', $data_id];
		}
		 
		return $where;
	}

	public function getGoods(){
		$where = $this->getUrlParam();
		$goods_list = GoodsModel::getCateData($where);
		return $goods_list;
	}
     
    
	
}
