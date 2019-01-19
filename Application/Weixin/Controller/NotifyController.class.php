<?php

namespace Weixin\Controller;

use Think\Controller;

class NotifyController extends Controller
{
    public function _initialize()
    {

    }

    //异步通知url，商户根据实际开发过程设定
    public function notify()
    {
        vendor('WxPayPubHelper.WxPayPubHelper');
        //使用通用通知接口
        $notify = new \Notify_pub();
        //存储微信的回调
        $xml = $GLOBALS['HTTP_RAW_POST_DATA'];
        $notify->saveData($xml);
        //验证签名，并回应微信。
        //对后台通知交互时，如果微信收到商户的应答不是成功或超时，微信认为通知失败，
        //微信会通过一定的策略（如30分钟共8次）定期重新发起通知，
        //尽可能提高通知的成功率，但微信不保证通知最终能成功。
        $data['remark'] = $xml;
        if ($notify->checkSign() == FALSE) {
            $data['nickname'] = "FAIL";
            $notify->setReturnParameter("return_code", "FAIL");//返回状态码
            $notify->setReturnParameter("return_msg", "签名失败");//返回信息
        } else {
            $data['nickname'] = "SUCCESS";
            $notify->setReturnParameter("return_code", "SUCCESS");//设置返回码
            $notify->setReturnParameter("return_msg", "OK");//返回信息
            $out_trade_no = $notify->data['out_trade_no'];
            //根据不同的支付支付修改状态
            $openid = $notify->data['openid'];
            $data['uid'] = M('user')->where('openid="' . $openid . '"')->getField('uid');
            switch ($notify->data['attach']) {
                //根据标示对不同的业务做相应的操作
            }
        }
        // 订单金额
        $a = $notify->returnXml();
        echo $a;//返回给微信结果
    }

    public function recharged()
    {
        $out_trade_no = I('out_trade_no');
        $total_fee = I('total_fee');
        $openid = $_SESSION['openid'];
        //获取用户的个人信息
        $userinfo = M('user')->where('openid="' . $openid . '"')->find();
        $this->assign('total_fee', $total_fee);
        $this->assign('out_trade_no', $out_trade_no);
        $this->assign('userinfo', $userinfo);
        $this->display();
    }

    public function donationlist()
    {
        $this->display();
    }
}