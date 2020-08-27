<?php
namespace app\home\controller;

//创建了公共分类模块,目的在于前后台都能用
//分类后台模块添加了,是否显示菜单的下拉框[表内加字段]
use app\home\model\Category;
use app\home\model\Menu;
use app\home\model\Goods;
use think\Controller;
use page\Page;

//首页控制器
//因为在home模块内,修改了config/app.php
//默认模块index为home,访问的时候不用传参
class Index extends Controller 
{

	public function getMenu(){
		$menu_list1 = Menu::getMenuList();  
    	$menu_list2 = Category::getMenuList();  
    	$arr = [];
    	//准备空数组,拿到sort,判断大小,小的往前放,大的往后放

    	//算法： 
    	//临时变量保存其中一个数6
    	//冒泡排序:[6, 34,55, 5, 6, 78, 200] 两两比较,交换位置
    	//两个循环
    	//一个判断
    	//判断内交换
    	//
    	$menu_list = array_merge($menu_list1, $menu_list2);
		$temp = '';
    	for ($i=0; $i < count($menu_list)-1; $i++) { 
    		for ($j=0; $j < count($menu_list)-1; $j++) { 
    			if($menu_list[$j]['sort'] > $menu_list[$j+1]['sort']){
	    			$temp = $menu_list[$j];
	    			$menu_list[$j] = $menu_list[$j+1];
	    			$menu_list[$j+1] = $temp;
    			}
    		}
    	}
     
    	return $menu_list;
	}

    public function index()
    {
    	
    	 
        //1. 导航 菜单表模型
        	//app/home/model/Menu.php
    	//$menu_list = array_merge($menu_list1 , $menu_list2);
    	//菜单实现：
    		/*
    		1. 查询菜单表 model=home
    		2. 查询分类表 条件 is_menu=1
    		3. 处理数据
    			循环上面的数据，组装数组 setListData
    				name 页面显示
    				url  页面跳转
    				sort 排序
    		4. 控制器合并数组,根据sort排序
    		5. 输出
				
    		*/
    	$menu_list = $this->getMenu();
		$this->assign('menu_list', $menu_list);

        //2. 分类 分类表模型
        	//app/home/model/Category.php
		$cate_list = Category::getMoreList();
		$this->assign('cate_list', $cate_list);

        //[ 对应后台文章模型 扩展
        	//3. 广告图 广告表模型
    	//]

        //4. 商品 商品表模型
        	//app/home/model/Goods.php
			//a. 商品信息

			$arr = [];
			//获取所有想在楼层显示的分类
			$f_data = Category::getIndexData();
			foreach ($f_data as $k => $v) {
				$arr[] = Goods::getCateData($v['id']);
			}


 
			 
         $this->assign('data', $arr);
        //[ 对应后台文章模型  扩展
	        //5. 帮助信息 帮助信息表模型
	        //6. 友情链接 友情链接表模型
        //]\
        return $this->fetch();
    }
}
