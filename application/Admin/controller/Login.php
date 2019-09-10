<?php
namespace app\Admin\controller;

use think\Db;
use think\Controller;
use think\captcha\Captcha;

class Login extends Controller
{

    public function login()
    {
        $this->assign('data', array('title'=>'一品禅堂'));
        return $this->fetch();
    }

    public function login_sub()
    {
        // 验证码验证
        if( !captcha_check($_POST['verify'])){
            return json(array('status'=>0,'msg'=>'验证码输入错误！'));exit;
        }else{
            $adminuser = Db::table('ct_adminuser')->where(array('username'=>$_POST['username'],'password'=>md5(md5($_POST['password']))))->find();
            if (!empty($adminuser)) {
                Session::set('adminuser',$adminuser);
                return json(array('status'=>1,'msg'=>'登陆成功！'));exit;
            }else{
                return json(array('status'=>0,'msg'=>'登陆失败！请检查账号'));exit;
            }
        }
    }

    public function verify()
    {
        $config =    [
            // 验证码字体大小
            'fontSize'    =>    34,    
            // 验证码位数
            'length'      =>    4,   
            // 关闭验证码杂点
            'useNoise'    =>    false, 
            // 中文字符 
            'useZh'       =>    false,
        ];
        $captcha = new Captcha($config);
        return $captcha->entry();    
    }
}