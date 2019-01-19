<?php

namespace Weixin\Controller;

use Think\Exception;

class weixinPayController extends HomeController
{
    public function _initialize()
    {
        parent::_initialize();
        $openid = $_SESSION['openid'];
        if (empty($openid)) {
            $openid = $this->authorize_openid();
            $_SESSION['openid'] = $openid;
        }
    }

    /**
     * 商城首页
     */
    public function index()
    {
        $this->display();
    }

    /**
     * 我要捐款
     */
    public function donation()
    {
        $openid = $_SESSION['openid'];
        if (empty($openid)) {
            $openid = $this->authorize_openid();
        }
        //获取用户的个人信息
        $userinfo = $this->getUserInfo($openid);
        if ($_POST) {
            try{
                $total_fee = I('total_fee', trim());
                $attach = I('attach');
                $body = I('body');
                $attach = $attach ? $attach : 'yijuan';
                vendor('WxPayPubHelper.WxPayPubHelper');
                if ($total_fee <= 0) {
                    throw new Exception('输入的消费金额必须大于0');
                }
                if (empty($total_fee)) {
                    throw new Exception('消费金额不可为空');
                }
                $jsApi = new \JsApi_pub();
                if (empty($_SESSION['openid'])) {
                    $openid = $this->authorize_openid();
                } else {
                    $openid = $_SESSION['openid'];
                }
                //使用统一支付接口，获取prepay_id
                $unifiedOrder = new \UnifiedOrder_pub();
                //设置统一支付接口参数
                //设置必填参数
                $total_fee = intval(floatval($total_fee) * 100);
                $unifiedOrder->setParameter("openid", $openid);//用户标识
                $unifiedOrder->setParameter("body", $body);//商品描述
                //自定义订单号，此处仅作举例
                $out_trade_no = uniqid() . time();
                $unifiedOrder->setParameter("out_trade_no", $out_trade_no);//商户订单号
                $unifiedOrder->setParameter("total_fee", $total_fee);//总金额
                $unifiedOrder->setParameter("notify_url", \WxPayConf_pub::NOTIFY_URL);//通知地址
                $unifiedOrder->setParameter("trade_type", "JSAPI");//交易类型
                $unifiedOrder->setParameter("attach", $attach);//交易类型
                switch ($attach) {
                    //根据不同的业务做不同的操作，支付需要的一些初识操作
                }
                $prepay_id = $unifiedOrder->getPrepayId();
                $a = 1;
                //通过prepay_id获取调起微信支付所需的参数
                $jsApi->setPrepayId($prepay_id);
                $jsApiParameters = $jsApi->getParameters();
                $wxconf = json_decode($jsApiParameters, true);
                if ($wxconf['package'] == 'prepay_id=') {
                    throw new Exception('当前订单存在异常，不能使用支付');
                }
                $result = array("status" => $a, "wxconf" => $wxconf, 'out_trade_no' => $out_trade_no);
                $this->ajaxReturn($result);
            }catch (Exception $e){
                $this->ajaxReturn(array('error_msg' => $e->getMessage(), "status" => 0,));
            }
        } else {
            $this->assign('userinfo', $userinfo);
            $this->display();
        }
    }
    /**
     * js微信支付
     */
    public function jspay(){
        $openid = $_SESSION['openid'];
        if(empty($openid)){
            $openid = $this->authorize_openid();
            $_SESSION['openid'] = $openid;
        }
        $jsapiTicket = $this->getJsApiTicket();
// 注意 URL 一定要动态获取，不能 hardcode.
        $protocol = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off' || $_SERVER['SERVER_PORT'] == 443) ? "https://" : "http://";
        $url = "$protocol$_SERVER[HTTP_HOST]$_SERVER[REQUEST_URI]";
        $timestamp = time();
        $nonceStr = $this->createNonceStr();
// 这里参数的顺序要按照 key 值 ASCII 码升序排序
        $string = "jsapi_ticket=$jsapiTicket&noncestr=$nonceStr&timestamp=$timestamp&url=$url";
        $signature = sha1($string);
        $signPackage = array(
            "appId"     => 'wx8b9258d4365e5deb',
            "nonceStr"  => $nonceStr,
            "timestamp" => $timestamp,
            "url"       => $url,
            "signature" => $signature,
            "rawString" => $string
        );
        $this->assign('signPackage',$signPackage);
        $this->display();
    }
}