<?php
namespace app\admin\controller;

use app\admin\common\Common;
use think\Db;
use think\facade\Request; 

/**
* 规格控制器
*/
class Spec extends Common
{ 
	/**
	* 添加方法

	1. 用户点击添加规则按钮
		a. 隐藏添加规则按钮 display:none
		b. 显示规则名输入框 显示规则值输入框 显示确定与取消按钮
		display:block
		$('添加按钮').click(function(){
			$(this).css('display', 'none')
			$('要显示的控制器').css('display', 'block')
		})

	2. 用户填写信息,点击确认,发起ajax
		$('按钮选择器').click(function(){
			$.post()
		})
	3. 控制器添加数据,返回结果
	{
		"code":1,
		"msg":"",
		"url":"",
		"data":{"spec_id":10005,"spec_value_id":10016}
	}
	4. js接收结果,提示
		a. 将结果展示到html中
		$('要显示的控制器').append('要显示的结果')

	*/
    public function add()
    {
    	//获取提交过来的数据
    	$data = Request::post();
     	//将规则名添加到数据表
     	//因为每个产品都要添加属性
     	//数据表重复数据太多
     	//解决:添加前查询,有的话,返回id
     	//没有则添加
     	$spec_data = Db::name('spec')->where([
     		'spec_name'=>$data['spec_name']
     	])->find();
     	if($spec_data){
     		$spec_id = $spec_data['id'];
     	}else{
     		$spec_id = Db::name('spec')->insertGetId([
		       	'spec_name'=>$data['spec_name'],
		       	'create_time'=>time()
		     ]);
     	}
        
        //将规则值添加到数据表
        $spec_value_id = Db::name('spec_value')->insertGetId([
       		'spec_id'=>$spec_id,
       		'spec_value'=>$data['spec_value'],
       		'create_time'=>time()
        ]);

        $data = ['spec_id'=>$spec_id,'spec_value_id'=>$spec_value_id];
 		return $this->sendMsg($spec_value_id, $data);
    }

    /**
    * 给规则组添加规则值
    1. shop_add.html 顶部追加添加规则值得url
    SPEC_ADD_VALUE_URL = '{:url("spec/addValue")}'
    2. 在goods.spec.js
    	修改 post的url
    3. 控制器添加addValue方法
    	a. 接受数据
    	b. 查询数据/添加数据
    	c. 组装数据，返回
    		'data' => ['spec_value_id'=>$spec_value_id]
    		{
    			"code":1,
	    		"msg":"",
	    		"url":"",
	    		"data":{"spec_value_id":"11"}
    		}
    */
    public function addValue(){

    	// 启动事务
		Db::startTrans();
		try {
		    //获取提交的数据
	    	$data = Request::post();
	 		
	 		$spec_value_id = $this->getSpecValueID($data);

	 		$data = ['spec_value_id'=>$spec_value_id];
	 		
		    // 提交事务
		    Db::commit();

		    return $this->sendMsg($spec_value_id, $data);
		} catch (\Exception $e) {
		    // 回滚事务
		    Db::rollback();
		    //失败的提示
		}

    	
    }

    /**
    * 获取规则值id
    */
    private function getSpecValueID($data){
    	$spec_data = Db::name('spec_value')->where([
    		'spec_id'=>$data['spec_id'],
       		'spec_value'=>$data['spec_value'],
    	])->find();
    	if($spec_data){
    		$spec_value_id = $spec_data['id'];
    	}else{
			$spec_value_id = Db::name('spec_value')->insertGetId([
	       		'spec_id'=>$data['spec_id'],
	       		'spec_value'=>$data['spec_value'],
	       		'create_time'=>time()
        	]);
    	}
    	return $spec_value_id;
    }

    private function sendMsg($spec_value_id, $data){
    	if($spec_value_id){ 
			$res_data = [
				'data'=> $data
			];
			return json(array_merge(config('data.spec'), $res_data));
		}
 		//失败的响应
		return json(config('data.spec'));
    }
}
