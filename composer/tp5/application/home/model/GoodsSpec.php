<?php 
namespace app\home\model;

use think\Model;
 
class GoodsSpec extends Model
{
 	public static function getGoodsId($data){
 		
 		//$arr = implode('_', $data);
 		$arr = explode('_', $data);
 		foreach ($arr as $k => $v) {
 			$where[] = ['spec_sku_id', 'like',  "$v"];
 			$where[] = ['spec_sku_id', 'like',  "%\_$v"];
 			$where[] = ['spec_sku_id', 'like',  "$v\_%"];
 		}
 
 		$data = self::field('goods_id')->whereOr($where)->group('goods_id')->select();

 		$arr = [];
 		foreach ($data as $key => $value) {
 			$arr[] = $value['goods_id'];
 		}
 		
 		return $arr;
 	}

 	public static function getSpcePrice($id){
 		return self::where(['spec_sku_id'=>$id])->find();
 	}

 	public static function getCartSpcePrice($id, $more=0){
 		if($more === 'more'){
 			// dump($more);die;
 			return self::where(['id'=>$id])
 			->select();
 		}
 		return self::where(['id'=>$id])
 		->find();
 	}
}