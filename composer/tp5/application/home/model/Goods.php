<?php 
namespace app\home\model;

use think\Model;
use app\home\model\GoodsSpecRel;
use app\home\model\Spec;
use app\home\model\SpecValue;


class Goods extends Model
{

	public static $where; 

	public function cate(){
/*
1对1的关联，join语句模型的实现
怎么做：
 1.建立要关联的模型
   例如：建立铲平与分类文件
 2.确认谁是主表，在谁里写关联
   例如：产品表的主表
 3.自定义方法（方法名一般是对应模型的模型）
 4.方法内返回
 $this->hasOne('要关联的模型名b','b模型id','a模型外键');
 hasOne('要关联的模型名b','b模型id','a模型外键');一对一
 5.获取对应数据：模型的对象->刚刚的方法名
 $data=Goods::get(21);
 dump($data->cate);die;
 */
      // 1.要关联的表  Category
      // 2.要关联表的id  id
      // 3.当前表的外键  category_id
		return $this->hasOne('Category', 'id', 'category_id');
	}
/*
1.对应标的模型 非中间表
2.中间表的模型
3.参数3与第一个参数模型的id对应
4.参数4与当前类对应标表的id对应
怎么做：
 1.建立要关联的模型
   例如：建立铲平与图片与产品图片中间表的文件
 2.确认谁是主表，在谁里写关联
   例如：产品表的主表
 3.自定义方法（方法名一般是对应模型的模型）
 4.方法内返回
 $this->belongsToMany('要关联的模型名b','中间模型c',b表的id在中间表对应的字段','a表的id在中间表对应的字段');
 5.获取对应数据：模型的对象->刚刚的方法名 注意不带小括号
*/

  /*
 模型关联
   1对1关联  1对多关联
   如何建立模型(建立几个)，由表的都键
   参数：记住
   使用：直接使用方法
 */
  // public function goodsImg(){
  //  return $this->belongsToMany()  多对多
  // }
	public function goodsImg(){
		return $this->belongsToMany('GoodsImage','GoodsImgData','image_id','goods_id');
	}
 
	public static function getCateData($where){
		self::$where = $where;
		if(is_array($where)){
			//查询商品数据
			$data = self::where($where)->paginate(4);

		}else{
			//查询商品数据
			$data = self::where([
				'category_id' => $where
			])->select();
		}
		

		//将联查内的图片拿出来和商品数据访一块
		foreach ($data as $k => $v) {

			$v->aaa = ''; 
			if(isset($v->goodsImg[0])){
				$v->aaa = $v->goodsImg[0]['src'];
			}

		}

		if(is_array($where)){
			//数据转数组返回
			return $data;
		}else{
			//数据转数组返回
			return $data->toArray();
		}
		
		// return self::where([
		// 	'category_id' => $cate_id
		// ])->select()->toArray();
	} 

		// $goods_data = Db::name('goods')
		// 				->alias('g')
		// 				->field('g.*,c.name as cate_name,i.src')
		// 				->join('tp_goods_img_data d', 'g.id=d.goods_id', 'left')
		// 				->join('tp_goods_image i', 'd.image_id=i.id', 'left')
		// 				->where($where)
		// 				->group('g.id')
		// 				->select();

	// 楼层：
	// 	显示：分类的商品
	// 	流程：
	// 		a. 查询要在首页楼层显示的分类
	// 		b. 根据要显示的分类id查询商品
	// 		c. 循环输出
	// 		d. 注意是二维数组

	// 		商品名  查询商品表
	// 		商品图  联查图片表
	// 		商品价格【商品表加个peice， 初始价格】
	// 		【后台放个输入框】 


	//【图片】 【标题】  【价格】  【划线价格】  【交易数】

	/*
		1. 主方法调用所有方法
			a. 根据条件获取商品id  getGoodsListId
			b. 根据商品id获取规格id  getSpecAndValueId
			c. 根据规格id获取name getSpecAndValueName
	*/
	//获取规格主方法  
		//没有goodsId获取一类商品规格
		//有goodsId获取单一商品规格
	public static function getSpec($goodsId=0){
		if(!$goodsId){
			$goodsId = self::getGoodsListId();
		}
		
		$spec_id = self::getSpecAndValueId($goodsId);
		// dump($spec_id);die;
		$data = self::getSpecAndValueName($spec_id);

		return $data;
	}
 

   //获取规格值与id
   //查询中间表 获取spec和valueid
	public static function getSpecAndValueId($goodsId){
		$spec_id = [];
		$spec_value_id = [];
		$data = GoodsSpecRel::where(['goods_id'=>$goodsId])->select();
		foreach ($data as $k => $v) {
			$spec_id[] = $v['spec_id'];
			$spec_value_id[] = $v['spec_value_id'];
		}

		//数组去重
		$spec_id = array_unique($spec_id);
		$spec_value_id = array_unique($spec_value_id);
		
		return [
				'spec_id'=>$spec_id,
				'spec_value_id'=>$spec_value_id
			];
	}

	//获取规格与值得name值
	public static function getSpecAndValueName($data){
		$spec_data = Spec::where(['id'=>$data['spec_id']])->select()->toArray();

		$spec_value_data = SpecValue::where(['id'=>$data['spec_value_id']])->select()->toArray();
	 
		$arr = [];
		foreach ($spec_data as $k => $v) {
			$arr[$k] = $v;
			foreach ($spec_value_data as $key => $value) {
				if($v['id'] == $value['spec_id']){
					$arr[$k]['child'][] = $value;
				}
			}
		}
		
		return $arr;
	}

	//获取商品id
	public static function getGoodsListId(){
		$data = self::where(self::$where)->select();
		$arr = [];
		foreach ($data as $k => $v) {
			$arr[] = $v['id'];
		}
		return $arr;
	}


	public static  function getCurCateGoods($cate_id, $goods_id){
		$data = self::field('id')->where(['category_id'=>$cate_id])->select();
		

		$arr = [];
		foreach ($data as $key => $value) {
			$arr[] = $value['id'];
		}
/*
   并集：array_merge（）

   交集：array_intersect（）

   差集：array_diff()
*/

		return array_intersect($goods_id, $arr);
	}


	public static function getDetailData($id){
		return self::where(['id'=>$id])->find();
	}
}