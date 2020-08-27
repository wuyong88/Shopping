<?php
namespace app\admin\controller;

use think\facade\Session;
use app\admin\common\Common;

class Index extends Common
{ 
    public function index()
    {
        return $this->fetch();
    }

    public function del()
    {
    	
    	echo 111;
    }
}
