<?php 
namespace app\home\model;

use think\Model;
use app\home\model\GoodsSpec;
use app\home\controller\Cart as CartContr;

class Cart extends Model
{
  	public static function batchInsertCart($data){

  		if(!$data){
  			return false;
  		}
  		
  		$where = [];
  		foreach ($data as $k => $v) {
  			$where[] = ['goods_id', '=', $v['id']];
  			$where[] = ['spec_key', '=', $v['spec_rel_id']];
  		}
  		//检查商品，规格是否一致
  		$goods_spec_data = self::whereOr($where)
      ->select(); 
  	   // dump($goods_spec_data)	;die;
	   
  		if($goods_spec_data){
  			self::BatchUpdateCartNum($goods_spec_data, $data);
  		}

		$arr = [];
		$spec_rel_id = [];
		foreach ($data as $k => $v) {
			foreach ($goods_spec_data as $key => $value) {
        // 当这两个数据不一样的时候放进arr[]
				if($value['goods_id'] != $v['id'] or $value['spec_key'] != $v['spec_rel_id']){
					$arr[] = $v;
					$spec_rel_id[] = $v['spec_rel_id'];
				}
			}
		}
     // dump($arr);die
  		if($arr){

  			$spec_data = GoodsSpec::getCartSpcePrice($spec_rel_id, 'more');

  			$cart = new CartContr;
  			foreach ($arr as $k => $v) {
          //购物车数据
  				$cart_data[] = $cart->getCartInsertData($v);
  			}
  			// dump($cart_data);die;
        //查所有
  			$car_model = new self;
  			$car_model->saveAll($cart_data);
  			
  		}
 
  	}

  	public static function BatchUpdateCartNum($goods_spec, $cart_data){
  		foreach ($goods_spec as $k => $v) {
  			foreach ($cart_data as $key => $value) {
  				if($value['id'] == $v['goods_id'] && $value['spec_rel_id'] == $v['spec_key']){
  					self::where(['id'=>$v['id']])
            ->setInc('goods_num', $value['num']); //setInc()基础上增加
  				}
  			}	
  		}
  	}

  	public static function insertCart($data){
  		$where[] = ['goods_id', '=', $data['goods_id']];
  		$where[] = ['spec_key', '=', $data['spec_key']];
  		$goods_spec_data = self::where($where)
      ->find();
         // dump($goods_spec_data);die;
  		if($goods_spec_data){
  			return self::where($where) //根据$where修改
        ->setInc('goods_num', $data['goods_num']);

  		}
  		return self::create($data);
  	}

  	public static function getCartData($user_id){
  		return self::where([
  			'user_id' => $user_id
  		])->select();
  	}
}