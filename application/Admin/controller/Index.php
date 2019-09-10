<?php
namespace app\Admin\controller;

use Think\Db;
use think\Controller;
use Session;

class Index extends Controller
{
    // 控制器初始化
    protected function initialize()
    {
        // 判断是否登录
        if (!(Session::has('adminuser'))) {
            $this->error('请重新登陆！', 'Admin/Login/login');
        }
    }

    public function index()
    {
        var_dump(Session::get('adminuser'));
        echo 'admin';
    }

}