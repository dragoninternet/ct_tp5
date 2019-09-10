<?php
// +----------------------------------------------------------------------
// | ThinkPHP [ WE CAN DO IT JUST THINK ]
// +----------------------------------------------------------------------
// | Copyright (c) 2006~2018 http://thinkphp.cn All rights reserved.
// +----------------------------------------------------------------------
// | Licensed ( http://www.apache.org/licenses/LICENSE-2.0 )
// +----------------------------------------------------------------------
// | Author: liu21st <liu21st@gmail.com>
// +----------------------------------------------------------------------

// +----------------------------------------------------------------------
// | 模板设置
// +----------------------------------------------------------------------

return [
    // 模板引擎类型 支持 php think 支持扩展
    'type'         => 'Think',
    // 默认模板渲染规则 1 解析为小写+下划线 2 全部转换小写 3 保持操作方法
    'auto_rule'    => 1,
    // 模板路径
    'view_path'    => '',
    // 模板后缀
    'view_suffix'  => 'html',
    // 模板文件名分隔符
    'view_depr'    => DIRECTORY_SEPARATOR,
    // 模板引擎普通标签开始标记
    'tpl_begin'    => '{',
    // 模板引擎普通标签结束标记
    'tpl_end'      => '}',
    // 标签库标签开始标记
    'taglib_begin' => '{',
    // 标签库标签结束标记
    'taglib_end'   => '}',
    //模板参数替换
    'tpl_replace_string' => array(
        '__STATIC__' => '/tp5/public/static',
        '__APP__'    => '/tp5/public/index.php'
    ),

];


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
        if (!(Session::has('name'))) {
            $this->error('请重新登陆！', 'Admin/Login/login');
        }
    }

    public function index()
    {
        var_dump(Session::get('adminuser'));
        echo 'admin';
    }

}



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




<!doctype html>
<html  class="x-admin-sm">
<head>
    <meta charset="UTF-8">
    <title>后台登录-{$data.title}</title>
    <meta name="renderer" content="webkit|ie-comp|ie-stand">
    <meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1">
    <meta name="viewport" content="width=device-width,user-scalable=yes, minimum-scale=0.4, initial-scale=0.8,target-densitydpi=low-dpi" />
    <meta http-equiv="Cache-Control" content="no-siteapp" />
    <link rel="stylesheet" href="__STATIC__/css/font.css">
    <link rel="stylesheet" href="__STATIC__/css/login.css">
      <link rel="stylesheet" href="__STATIC__/css/xadmin.css">
    <script type="text/javascript" src="__STATIC__/js/jquery.min.js"></script>
    <script src="__STATIC__/lib/layui/layui.js" charset="utf-8"></script>
    <!--[if lt IE 9]>
      <script src="https://cdn.staticfile.org/html5shiv/r29/html5.min.js"></script>
      <script src="https://cdn.staticfile.org/respond.js/1.4.2/respond.min.js"></script>
    <![endif]-->
    <style type="text/css">
      h3 { font-weight: bold; }
      .verify_div { position: relative; }
      .verify_div input{ float: left;width: 200px; }
      .verify_div .verify img{ width: 140px;height:48px;position: absolute;top:1px;right: 1px; }
    </style>
</head>
<body class="login-bg">
    
    <div class="login layui-anim layui-anim-up">
        <div class="message"><h3>{$data.title}-管理登录</h3></div>
        <div id="darkbannerwrap"></div>
        
        <form method="post" class="layui-form" >
            <input name="username" placeholder="用户名"  type="text" lay-verify="required" class="layui-input" >
            <hr class="hr15">
            <input name="password" lay-verify="required" placeholder="密码"  type="password" class="layui-input">
            <hr class="hr15">
            <div class="verify_div">
              <input name="verify" lay-verify="required" placeholder="验证码"  type="text" class="layui-input" style="position: relative;">
              <span class="verify"><img src="{:url('Admin/login/verify')}" alt="captcha" /></span>
            </div>
            <hr class="hr15">
            <input value="登录" lay-submit lay-filter="login" style="width:100%;" type="submit">
            <hr class="hr20" >
        </form>
    </div>

    <script>
        $(function  () {
            layui.use('form', function(){
              var form = layui.form;
              //监听提交
              form.on('submit(login)', function(data){
                $.ajax({
                  url:"{:url('Admin/login/login_sub')}",
                  type:"POST",
                  data:{ username:data.field.username, password:data.field.password, verify:data.field.verify },
                  success:function(result){
                    if (result.status==1) {
                      layer.msg(result.msg);
                      location.href="{:url('Admin/Index/index')}";
                    }else{
                      layer.msg(result.msg);
                    }
                  }
                });
                return false;
              });
            });

            //验证码刷新
            $(".verify").click(function(data) {
              var time = new Date().getTime();
              var url = '__APP__/Admin/Login/verify/'+time;
              $(".verify img").attr('src',url);
            })
        })
    </script>
    <!-- 底部结束 -->
</body>
</html>


