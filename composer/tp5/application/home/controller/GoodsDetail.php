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


class GoodsDetail extends Goods 
{
	protected $img = [];

 	public function detail(){
 		$id = Request::param('id');

 		//商品信息
 		$data = GoodsModel::getDetailData($id);
// dump($data);die;
 		//商品图片  goodsImg
 		$this->img = $data->goodsImg;
 		$this->getImg();
 		// dump($this->getImg());die;

 		// GoodsModel
 		//商品规格
 		$spec = GoodsModel::getSpec($id);
        // dump($spec);die;
 		$this->assign([
 			'spec' => $spec,
 			'data' => $data,
 			'img'  => $this->img
 		]);	

 		return $this->fetch('pro/Productintroduction');
 	}


 	public function getImg(){
 		$arr = [];
 		foreach ($this->img as $k => $v) {
 			$arr[] = $v['src'];
 		}
 		$this->img = $arr;
 	}

 	public function getSpecPrice(){
 		$spec_id = Request::param('spec_sku_id');
 		$data = GoodsSpec::getSpcePrice($spec_id);
  // dump($data);die;
 		return json([
 			'data' => $data
 		]);
 	}
}
