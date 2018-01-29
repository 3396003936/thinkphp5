<?php
namespace app\Index\controller;

class HelloWord
{
    public function index()
    {
        return 'url: ' . request()->url() . '<br/>';
//        return 'hello word!';
    }

}
