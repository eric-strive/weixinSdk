<?php
/**
 * 个人中心
 */
namespace Weixin\Controller;
class IndexController extends HomeController
{
    public function _initialize(){
        parent::_initialize();
    }
    /**
     * 我要充值
     */
    public function start_recharge(){
        $this->display();
    }
}