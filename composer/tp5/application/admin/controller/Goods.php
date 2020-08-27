<?php
namespace app\admin\controller;

use app\admin\common\Common;
use think\Db;
use think\facade\Request; //请求类

/**
* 新模块的创建
	1. 建表
	2. controller文件夹下面新建控制器
		a. 继承Common
	3. 此控制器增删改查基本操作
	4. view文件夹下新建对应控制器的文件夹,把html放进去
随时

产品模块

	1. tp5.1创建控制器 [ php think build ]
		命名空间
		文件名与类名一致
		浏览器访问的是controller控制器的文件,控制器use其他命名空间
	2. 控制器加载html
		控制器集成基类
		view目录下的文件夹 与控制器类名对应
		文件名 与控制器方法名一致
		fetch方法('模块名/view目录下的文件夹/文件名')
	3. 视图
		输出  变量的输出{} config/template.php
		{if 条件} {else} {/if}  
		{foreach} {/foreach}
		{:方法名(参数1,参数2)}
		$Think.session
		$Think  cookie  post get/.....
		<?=$_SESSION['11']?>

	业务:
		增删改查 农民工

		权限
		规格
		


*/
class Goods extends Common
{
	//属性 post的数据
	protected $post_data = [];

	/**
	* 初始化 接受post数据
	*/
	public function initialize(){
		//调用父类方法，先执行父类方法，在执行自己
		parent::initialize(); 
		//获取post提交的数据
		$this->post_data = Request::post();
	}

	/**
	* 获取要查询的参数
	*/
	public function getWhere(){
		//图片显示
		// 查询goods表，关联商品图片表，关联图片表，查询会重复，需要根据商品id[group]
		// 【三表联查】
		// //查询条件m
		// get提交条件数据
		// 获取数据，拼接条件
		// 	[	
		// 		[字段，比较符号，值]
		// 		[字段，比较符号，值]
		// 		[字段，比较符号，值]
		// 		[字段，比较符号，值]
		// 		[字段，比较符号，值]
		// 	]
		// 【条件拼接】
		// 将上面所有作为查询条件
		// 注意： fetchSql(true) 可以打印sql语句

		$get_data = Request::get();
		
		$where = [['is_delete', '=', 0]];

		if(isset($get_data['category_id']) && $get_data['category_id'] > 0){
			$where[] = ['category_id', '=', $get_data['category_id']];
		}

		if(isset($get_data['status'])){
			$where[] = ['goods_status', '=', $get_data['status']];
		}

		if(isset($get_data['search']) && $get_data['search'] != ''){
			$where[] = ['goods_name', 'like', '%'.$get_data['search'].'%'];
		}
		return $where;
	}

	/**
	* 商品首页
	*/
	public function index(){
		//实现分页
		//1. sql查询将select() 改为 paginate()
		//2. paginate() 传入参数: 每页显示条数
		//3. html在要分页的地方 {数据变量|raw}
		//4. html显示总数据量的地方 {数据变量->total()}
 		$where = $this->getWhere();

		$goods_data = Db::name('goods')
						->alias('g')
						->field('g.*,c.name as cate_name,i.src')
						->join('tp_category c', 'c.id=g.category_id', 'left')
						->join('tp_goods_img_data d', 'g.id=d.goods_id', 'left')
						->join('tp_goods_image i', 'd.image_id=i.id', 'left')
						->where($where)
						->group('g.id')
						//->fetchSql(true)
						//->select();
						->paginate(10, false, ['query'=> Request::param()]);
  
		$this->assign('goods_data', $goods_data);
		return $this->fetch('shop_list');
	}

	/**
	* 商品修改
	*/
	public function edit(){
		//1. 商品信息
		//2. 图片信息 循环
		//3. 规格显示 循环
		$id = Request::param('id');
		//商品信息
		$goods_data = Db::name('goods')->where(['id'=>$id])->find();

		//图片信息
		$img_data = Db::name('goods_img_data')
			->alias('d')
			->field('d.id,i.src,d.image_id')
			->join('tp_goods_image i', 'd.image_id=i.id')
			->where(['d.goods_id'=>$id])
			->select();
		 
		//商品规格
		$spec_rel_data = Db::name('goods_spec_rel')->where([
			'goods_id' => $id
		])->select();

		//查到了规格名
		$spec_id = [];
		foreach ($spec_rel_data as $k => $v) {
			$spec_id[] = $v['spec_id'];
		}
		$spec_data = Db::name('spec')->where([
			'id'=> $spec_id
		])->select();

		//查询规格值
		$spec_value_id = [];
		foreach ($spec_rel_data as $k => $v) {
			$spec_value_id[] = $v['spec_value_id'];
		}
		$spec_value_data = Db::name('spec_value')->where([
			'id'=> $spec_value_id
		])->select();

		//规则名与规则值对应
		//循环规则名，同时循环规则值，如果spec_id相等组装
		//分类树
		$spec = [];
		foreach ($spec_data as $k => $v) {
			$spec[$k]['group_id'] = $v['id'];
			$spec[$k]['group_name'] = $v['spec_name'];
			$i=0;
			foreach ($spec_value_data as $key => $value) {
				if($v['id'] == $value['spec_id']){
					$spec[$k]['spec_items'][$i]['item_id'] =  $value['id'];
					$spec[$k]['spec_items'][$i]['spec_value'] =  $value['spec_value'];
					$i++;
				}
			}
			
		}
 
		// group_id: result.data['spec_id'],
  //       group_name: _this.addGroupFrom.specName,
  //       spec_items: [{
  //               item_id: result.data['spec_value_id'],
  //               spec_value: _this.addGroupFrom.specValue
  //       }],
                          
		//规格价钱详细数据
		$goods_spec = Db::name('goods_spec')->where([
			'goods_id' => $id
		])->select();

		$arr = [];
		$_spec_value_data = [];

		//目的:准备将goods_spec 里面的 spec_sku_id的数据转值
		foreach ($spec_value_data as $k => $v
		) {
			$_spec_value_data[$v['id']] = $v['spec_value'];
		}

		foreach ($goods_spec as $k => $v) {
			$arr[$k]['form'] = $v;

			$spec_sku_id = explode('_', $v['spec_sku_id']);

			foreach ($spec_sku_id as $key => $value) {
		 
				$arr[$k]['rows'][] = [
					'item_id'=>$value,
					'rowspan'=>1,
					'spec_value'=>$_spec_value_data[$value]
				];
			}
 
			$arr[$k]['spec_sku_id'] = $v['spec_sku_id'];
		}	 
 

		$this->assign([
			'spec' => $spec,
			'img_data' => $img_data,
			'goods_spec' => $arr,
			'goods_data' => $goods_data,
			'id'=>$id
		]);

		return $this->fetch('shop_edit');
	}
	/**
	* 商品添加
	*/
	public function add(){
		return $this->fetch('shop_add');
	}

	public function setData(){
		$data =   $data_ =  Request::param();
		 
		// 启动事务
		Db::startTrans();
		try {
		
		//1. 修改商品基本信息
		unset($data['goods']['sku']);
	 	unset($data['goods']['more']);
	 	unset($data['goods']['spec']);
		unset($data['goods']['selling_point']); //暂时的
		unset($data['goods']['images']); //不是暂时的

		$goods = Db::name('goods')->where([
			'id' => $data['id']
		])
		->update($data['goods']);


		//2. 修改图片信息
			$img_data = [];
			//获取post过来的图片id
			$img = $data_['goods']['images'];
			//循环所有图片id 组装待添加数组
			foreach ($img as $k => $v) {
				$img_data[] = ['goods_id'=>$data['id'], 'image_id'=>$v];
			}
			
			Db::name('goods_img_data')->where(['goods_id'=>$data['id']])->delete();
			//添加
			$res = Db::name('goods_img_data')->insertAll($img_data);

			//sleep(秒) //休眠 程序到这里停止 时间到了再执行

			$id = $data['id'];
			$data = $data_['goods'];
			//3. 修改规格

			if($data['spec_type'] == 10){
					$spec_res = Db::name('goods_spec')->where(['goods_id' => $id])->update([
					  'goods_no' => $data['sku']['goods_no'],
					  'goods_price'  => $data['sku']['goods_price'],
					  'line_price' => $data['sku']['line_price'],
					  'stock_num' => $data['sku']['stock_num'],
					  'goods_sales'  => 0,
					  'goods_weight'  => $data['sku']['goods_weight'],
					  'spec_sku_id'  => '',
					  'update_time'  => time()
					]);
			}else{
				$goods_spec_data = $this->getGoodsSpecData($id);
				Db::name('goods_spec')->where(['goods_id' => $id])->delete();
				Db::name('goods_spec')->insertAll($goods_spec_data);

				$goods_spec_rel_data = $this->getGoodsSpecRelData($id);
				Db::name('goods_spec_rel')->where(['goods_id' => $id])->delete();
				Db::name('goods_spec_rel')->insertAll($goods_spec_rel_data);

			}
		 
  			// 提交事务
		    Db::commit();

		    return json(array_merge(config('data.success'),['url'=>url('index')]));

		} catch (\Exception $e) {
		    // 回滚事务
		    Db::rollback();
		    return json(config('data.error'));
		}
 
		

	}
	/**
	* 商品删除
	*/
	public function del(){
		return $this->update([
			'id'=>$this->post_data['goods_id']], 
			['is_delete'=>1]
		);
	}

	/**
	* 商品上下架
	*/
	public function setGoodsStatue(){
		// //获取post提交的数据
		// $data = Request::post();
		//goods_id   state
		//判断状态
		$state = $this->post_data['state'] == 10 ? 20 : 10;

		return $this->update([
			'id'=>$this->post_data['goods_id']], 
			['goods_status'=>$state]);
	}

	public function getGoodsSpecData($goods_id){
		/**
			总结
				html使用了vue去获取数据
				v-for循环数据   v-for="(值, 下标) in 数据"
				使用： {{ 值.spec_value }}
			
			小重点1：
				vue将js中的变量在html属性中使用 需要绑定
				v-bind:属性名="这里写js代码"
				<input v-bind:value="item.spec_sku_id">  

			小重点2：
				input传递数组  <input name="xxx[][][][]">

<span>{{ val.spec_value }}</span>
<input type="hidden" v-bind:name="'goods[spec]['+item.group_id+'][child][]'" v-bind:value="val.item_id">

<tr v-for="(item, index) in spec_list" >
<input type="hidden" name="goods[more][spec_value_id][]
" v-bind:value="item.spec_sku_id"> 

商品编号
<input  type="text" class="ipt-goods-no " name="goods[more][goods_no][]" 


				php获取并改造

				控制器获取数据  
				组装要插入的数据 
				完成
		*/
		$data = $this->post_data['goods']['more'];

		if(!$data['spec_value_id']){
			return false;
		}

		$arr = [];
		foreach ($data['spec_value_id'] as $k => $v) {
			$arr[] = [
						'goods_id' => $goods_id,
						'spec_sku_id' => $v,
						'goods_no' => $data['goods_no'][$k],
						'goods_price' => $data['goods_price'][$k],
						'line_price' => $data['line_price'][$k],
						'stock_num' => $data['stock_num'][$k],
						'goods_weight' => $data['goods_weight'][$k],
					];
		}
		
		return $arr;
	}

	/**
	 获取商品 规格 规格值 关联的数据
	*/
	public function getGoodsSpecRelData($goods_id){
		$data = $this->post_data['goods']['spec'];
		$arr = [];
		foreach ($data as $k => $v) {
			foreach ($v['child'] as $key => $value) {
				$arr[] = [
					'goods_id' => $goods_id, 
					'spec_id' => $v['spec_id'], 
					'spec_value_id' => $value
				];
			}
		}
		return $arr;
	}

	/**
	* 添加数据
	*/
	public function addData(){
		//整体目的： 节省空间  提升检索速度
		//没有文字冗余字段存在（外键作为表关联依据）
		//每张表很干净
		
 		// 规则 四张表
 		// 规则表  存放规则名       
 		// 规则值表 存放规则值      

		//						   
 		// 商品规则对应  表     目的：获取商品不同规格信息
 		// 不同规则不同价格 表      
 
 		
 		 
		// 启动事务
		Db::startTrans();
		try {
		    
			$data = $this->post_data['goods'];
	 		unset($data['sku']);
	 		unset($data['more']);
	 		unset($data['spec']);
			unset($data['selling_point']); //暂时的
			//商品表没有images字段,需要去掉
			unset($data['images']); //不是暂时的
			$id = Db::name('goods')->insertGetId($data);
			//声明空变量 存放待添加数据
			$img_data = [];
			//获取post过来的图片id
			$img = $this->post_data['goods']['images'];
			//循环所有图片id 组装待添加数组
			foreach ($img as $k => $v) {
				$img_data[] = ['goods_id'=>$id, 'image_id'=>$v];
			}
			//添加
			$res = Db::name('goods_img_data')->insertAll($img_data);
			//sleep(秒) //休眠 程序到这里停止 时间到了再执行



			//添加规则
			if($data['spec_type'] == 10){
					$spec_res = Db::name('goods_spec')->insert([
					  'goods_id' => $id,
					  'goods_no' => $data['sku']['goods_no'],
					  'goods_price'  => $data['sku']['goods_price'],
					  'line_price' => $data['sku']['line_price'],
					  'stock_num' => $data['sku']['stock_num'],
					  'goods_sales'  => 0,
					  'goods_weight'  => $data['sku']['goods_weight'],
					  'spec_sku_id'  => '',
					  'create_time'  => time(),
					  'update_time'  => time()
					]);
			}else{
				$goods_spec_data = $this->getGoodsSpecData($id);
				Db::name('goods_spec')->insertAll($goods_spec_data);

				$goods_spec_rel_data = $this->getGoodsSpecRelData($id);
				Db::name('goods_spec_rel')->insertAll($goods_spec_rel_data);

			}

		//     // 提交事务
		    Db::commit();

		    return json(array_merge(config('data.success'),['url'=>url('index')]));

		} catch (\Exception $e) {
		    // 回滚事务
		    Db::rollback();
		    return json(config('data.error'));
		}
 
	}

	private function update($where, $data){
		$res = Db::name('goods')->where($where)->update($data);
		//返回数据
		if($res){
			return json(array_merge(config('data.success'),['url'=>url('index')]));
		}
		return json(config('data.error'));
	}
}