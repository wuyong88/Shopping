<?php 
namespace app\common\util;
/**
* 前后台公共的目录
|-admin
|-home
|-common
	|-util 工具类
		|-File.php 文件工具类
*/
class File
{
	/**
	* 列表
	*/
	public function list(){}

	/**
	* 删除
	*/
	public function del(){}

	/**
	* 修改
	*/
	public function edit(){}

	/**
	* 添加
	*/
	public function add(){
		 // 获取表单上传文件 例如上传了001.jpg
	    $file = request()->file('file');
	    // 移动到框架应用根目录/uploads/ 目录下
	    $info = $file->move( '../uploads');
	    if($info){
	        // 成功上传后 获取上传信息
	        return $info->getSaveName();
	    }else{
	        // 上传失败获取错误信息
	        //echo $file->getError();
	        return '';
	    }
	}
}