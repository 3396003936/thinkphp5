<?php
namespace app\index\controller;

use think\Controller;
use think\Db;

class Index extends Controller
{
    public function index()
    {
        $data = Db::name('data')->select();
        $this->assign('result', $data);
        return $this->fetch();
    }

    public function hello($name = 'World')
    {
        return 'Hello,' . $name . '!';
    }

}
