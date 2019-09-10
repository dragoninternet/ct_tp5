<?php
namespace app\index\controller;
use think\Controller;

class Index extends Controller
{
    public function index()
    {
        $list = db('admin')->where('id',1)->find();
        //var_dump($list);
        $this->assign('list', $list);
        return $this->fetch();
    }

}
