<?php
namespace app\home\validate;

use think\Validate;

class User extends Validate
{
	//  array(4) {
//   ["username"] =&gt; string(3) "123"
//   ["password"] =&gt; string(3) "213"
//   ["qr_password"] =&gt; string(3) "123"
//   ["yzm"] =&gt; string(3) "123"
// }
    protected $rule = [
        'username'  =>  'require|mobile',
        'password' =>  'require|alphaDash|length:4,12',
        'qr_password' =>  'require|confirm:password',
        'yzm' => 'require|captcha'
    ];

    protected $message  =   [
        'username.require' => '手机号必须填写',
        'username.mobile'     => '必须填写有效手机号',
        'password.require'   => '密码必须填写',
        'password.alphaDash'  => '密码必须数字字母下换线与中横线',
        'password.length'        => '密码4-12位', 
        'qr_password.require'        => '密码保持一致',     
        'qr_password.confirm'        => '密码保持一致', 
        'yzm.require' => '验证码必须',
        'yzm.captcha' => '验证码不一致'
    ];

    protected $scene = [
        'login'  =>  ['username','password'],
    ];

}