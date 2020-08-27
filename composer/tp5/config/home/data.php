<?php 
/**
* 页面常用数据

* tp所有的配置文件 只需return数组
['status'=>102, 'msg'=>'账号不对'];
['status'=>101, 'msg'=>'没数据'];
['status'=>103, 'msg'=>'密码不对'];
['status'=>200, 'msg'=>'成功', 'url'=> url('index/index')];

文件位置: config/admin/data.php
*/
return [
	//公共的
	'empty_data' => [
		'status'=>101, 
		'msg'=>'没数据'
	],
	'success' => [
		'status'=>200,
		'msg'=>'成功'
	],
	'error' => [
		'status'=>104,
		'msg'=>'操作失败'
	],
	'sub_error' => [
		'status'=>105,
		'msg'=>'有下级不能删除'
	],
	//登录
	'login' => [
		'name_error' => [
			'status'=>102, 
			'msg'=>'账号不对'
		],
		'pwd_error' => [
			'status'=>103, 
			'msg'=>'密码不对'
		]
	],
	'spec' => [	
		"code"=>1,
		"msg"=>"",
		"url"=>""
		]
];