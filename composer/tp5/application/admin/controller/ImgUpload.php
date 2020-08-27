<?php 
namespace app\admin\controller;
/**
* 图片上传类
* 后台业务在这里

* 图片上传 ,请求这个类
 upload.render({
    elem: '#test1'
    ,url: '{:url("ImgUpload/add")}'
*/
use app\common\util\File;
use think\facade\Request;
use think\Db; //数据库类

class ImgUpload extends File
{
	public function add(){
		$data = parent::add();
		$res = Db::name('goods_image')->insertGetId([
			'src' => $data,
			'create_time' => time()
		]);

		if($res){
			
			return json(array_merge(config('data.success'),['id'=>$res]));
		}else{
			return json(config('data.error'));
		}
		//return $file;
	}
}