<?php
namespace Home\Controller;
use OT\DataDictionary;
header("access-control-allow-origin:*");
class JmrController extends HomeController{
    public $jmrapi;
    public function _initialize(){
        parent::_initialize();
        $this->jmrapi = new \Home\ORG\Util\Jmr();
    }
    /**
     * 百度api
     */
    public function baidu(){
        $this->auth_params['ak'] = "37492c0ee6f924cb5e934fa08c6b1676";
        $wechatBaiduAPI = new \Home\ORG\Util\WechatBaiduAPI();
        $ret = $wechatBaiduAPI->getDistance(39.915,116.40,39.925,116.40);
        dump($ret);exit;
    }
    /**
     * 个人中心页面
     * .带扫码的接口
     */
    public function index(){
        $openid = $_SESSION['openid'];
        if(empty($openid)){
            $openid = $this->authorize_openid();
            $_SESSION['openid'] = $openid;
        }
        //获取用户的个人信息
        $userinfo = M('user')->where('openid="'.$openid.'"')->find();
        $info = $this->jmrapi->query_vip($openid);
        if($userinfo['user_balance']!=$info['user_balance']||$userinfo['user_integral']!=$info['user_integral']){
            M('user')->where('openid="'.$openid.'"')->save(array('user_integral'=>$info['user_integral'],'user_balance'=>$info['user_balance']));
            $userinfo['user_balance'] = $info['user_balance'];
            $userinfo['user_integral'] = $info['user_integral'];
        }
        //查询接口用户信息
        if($userinfo['jmr_id']==0){
            M('user')->where('openid="'.$openid.'"')->save(array('jmr_id'=>$info['id']));
            $_SESSION['jmr_id'] = $info['id'];
            $userinfo['jmr_id'] = $info['id'];

        }
        $this->assign('userinfo',$userinfo);
        $this->display();
    }
    /**
     * 用户注册
     */
    public function register(){
//        $openid = $_SESSION['openid'];
//        if(empty($openid)){
//            $openid = $this->authorize_openid($openid);
//        }
////        $mobile = I('mobile');
//        $mobile = '18207199230';
//        //先保存手机号
//        M('user')->where('openid="'.$openid.'"')->setField("mobile",$mobile);
//        $return = $this->jmrapi->add_vip($openid,$mobile);
//        $return = $this->jmrapi->update_vip($openid,$mobile);
//        if($return['result']==0){
//            //修改个人信息保存
//            $userinfo = M('user')->where('openid="'.$openid.'"')->find();
//            $params = array(
//                'user_sex'=>$userinfo['sex'],
//                'user_birthday'=>'0',
//                'openid'=>$openid,
//                'user_name'=>$userinfo['nickname'],
//            );
//            $this->jmrapi->update_vip($params);
//            $this->success('添加成功');
//        }
        $this->display();
    }
    /**
     *用户信息修改
     */
    public function edituserinfo(){
        $openid = $_SESSION['openid'];
        if(empty($openid)){
            $openid = $this->authorize_openid();
            $_SESSION['openid'] = $openid;
        }
        if($_POST){
            $pro = I("pro");
            if(is_numeric($pro)){
                $userinfo['province'] = M('area')->where('area_id='.$pro)->getField('title');
            }else{
                $userinfo['province'] = $pro;
            }
            $userinfo['city'] = I("city");
            $userinfo['nickname'] = I("nickname");
            $userinfo['user_birthday'] = I("user_birthday");
            $userinfo['sex'] = I("sex");
            $params = array(
                'user_sex'=> I("sex"),
                'user_birthday'=> I("user_birthday"),
                'openid'=> $openid,
                'user_name'=> I("nickname"),
                'user_province'=>$userinfo['province'],
                'user_city'=> I("city"),
            );
            $result = $this->jmrapi->update_vip($params);
            if($result['result']==0){
                M('user')->where('openid="'.$openid.'"')->save($userinfo);
                $this->success('修改成功');
            }else{
                $this->error('修改失败');
            }
        }else{
            //省
            $pro = M('area')->where('pid=0')->select();
            //获取用户的个人信息
            $userinfo = M('user')->where('openid="'.$openid.'"')->find();
            $this->assign('pro',$pro);
            $this->assign('userinfo',$userinfo);
            $this->display();
        }
    }
    public function city(){
        $selValue = I('selValue');
        $city = M('area')->where('pid='.$selValue)->select();
        $this->ajaxReturn($city);
    }
    /**
     * 充值记录
     */
    public function rechargelist(){
        $openid = $_SESSION['openid'];
        if(empty($openid)){
            $openid = $this->authorize_openid($openid);
        }
        $list = $this->jmrapi->query_recharge($openid);
        $this->assign("list",$list);
        $this->display();
    }
    /**
     * 消费记录
     */
    public function consumelist(){
        $openid = $_SESSION['openid'];
        if(empty($openid)){
            $openid = $this->authorize_openid();
            $_SESSION['openid'] = $openid;
        }
        $list = $this->jmrapi->query_vipsale($openid);
        $this->assign("list",$list);
        $this->display();
    }
    /**
     * 会员充值
     */
    public function recharge(){
        $openid = $_SESSION['openid'];
        if(empty($openid)){
            $openid = $this->authorize_openid();
            $_SESSION['openid'] = $openid;
        }
        //获取用户的个人信息
        $userinfo = M('user')->where('openid="'.$openid.'"')->find();
        if($_POST){
            $money = I('money');
            $phone = I('phone');
            vendor('WxPayPubHelper.WxPayPubHelper');
            //使用jsapi接口
            $jsApi = new \JsApi_pub();
            if(empty($_SESSION['openid'])){
                $openid = $this->authorize_openid();
            }else{
                $openid = $_SESSION['openid'];
            }
            //使用统一支付接口，获取prepay_id
            $unifiedOrder = new \UnifiedOrder_pub();
            //设置统一支付接口参数
            //设置必填参数
            $total_fee = $money;
            $remark = $_POST['remark'];
            $total_fee = intval(floatval($total_fee)*100);
            $order_id = $_POST['order_id'];
//            $body = $_POST['body'];
            $body = "ICE机摩人会员充值";
            $unifiedOrder->setParameter("openid", $openid);//用户标识
            $unifiedOrder->setParameter("body", $body);//商品描述
            //自定义订单号，此处仅作举例
            $out_trade_no = uniqid().time();
            $unifiedOrder->setParameter("out_trade_no", $out_trade_no);//商户订单号
            $unifiedOrder->setParameter("total_fee", $total_fee);//总金额
            //$unifiedOrder->setParameter("attach", "order_sn={$res['order_sn']}");//附加数据
            $unifiedOrder->setParameter("notify_url", \WxPayConf_pub::NOTIFY_URL);//通知地址
            $unifiedOrder->setParameter("trade_type", "JSAPI");//交易类型
            $prepay_id = $unifiedOrder->getPrepayId();
            $data['out_trade_no'] = $out_trade_no;
            $data['money'] = $total_fee/100;
            $data['body'] = $body;
            $data['openid'] = $openid;
            $data['remark'] = $remark;
            $data['nickname'] = $userinfo['nickname'];
            $data['sex'] = $userinfo['sex'];
            $data['addtime'] = time();
            $data['status'] = 0;
            $data['imgurl'] = $userinfo['headimgurl'];
            $a = M('donation')->add($data);
            $a = 1;
            //通过prepay_id获取调起微信支付所需的参数
            $jsApi->setPrepayId($prepay_id);
            $jsApiParameters = $jsApi->getParameters();
            $wxconf = json_decode($jsApiParameters, true);
            if ($wxconf['package'] == 'prepay_id='){
                dump("当前订单存在异常，不能使用支付");exit;
                $this->ajaxReturn(array('error_msg' => '当前订单存在异常，不能使用支付'));
            }
            $result = array("status"=>$a,"wxconf"=>$wxconf,'out_trade_no'=>$out_trade_no);
            $this->ajaxReturn($result);
        }else{
            $cofig = $this->jmrapi->canshu();
            $cn = ($cofig['recharge_scale']-100)/100;
            $arr[1] = $cn*100;
            $arr[2] = $cn*200;
            $arr[3] = $cn*500;
            $this->assign('cn',$arr);
            $this->assign('userinfo',$userinfo);
            $this->display();
        }
    }
    public function recharged(){
        $openid = $_SESSION['openid'];
        if(empty($openid)){
            $openid = $this->authorize_openid();
            $_SESSION['openid'] = $openid;
        }
        //获取用户的个人信息
        $userinfo = M('user')->where('openid="'.$openid.'"')->find();
        $money = I('money');
        $phone = I('phone');
        $result = $this->jmrapi->viprecharge($userinfo['jmr_id'],$openid,$money,$phone);
        if($result['result']==0){
            $this->assign('rechargedinfo','充值成功');
        }else{
            $this->assign('rechargedinfo','充值失败');
        }
        $this->assign('userinfo',$userinfo);
        $this->assign('money',I('money'))   ;
        $this->display();
    }
    /**
     * 积分兑换
     */
    public function jifen(){
        $openid = $_SESSION['openid'];
        if(empty($openid)){
            $openid = $this->authorize_openid();
            $_SESSION['openid'] = $openid;
        }
        //获取用户的个人信息
        $userinfo = M('user')->where('openid="'.$openid.'"')->find();
        if($_POST){
            $jifen = I('jifen');
            $result = $this->jmrapi->use_integral($openid,$userinfo['jmr_id'],$jifen);
            if($result['result']==0){
                $this->succes('兑换成功');
            }else{
                $this->error('失败');
            }
        }else{
            $cofig = $this->jmrapi->canshu();
            $list = $this->jmrapi->integralrecord($openid);
            $this->assign('cn',$cofig['integral_scale']);
            $this->assign('list',$list);
            $this->assign('userinfo',$userinfo);
            $this->display();
        }
    }
    /**
     * 积分明细
     */
    public function integratelist(){
        $openid = $_SESSION['openid'];
        if(empty($openid)){
            $openid = $this->authorize_openid();
            $_SESSION['openid'] = $openid;
        }
        $list = $this->jmrapi->integralrecord($openid);
        $this->assign("list",$list);
        $this->display();
    }
    /**
     * 用户协议
     */
    public function useragreement(){
        $this->display();
    }
    /**
     * 用户支付
     * 链接需要包含sale_money=价格，out_trade_no=订单号，subject=冰淇淋名称，terminal_id=设备名称，num=商品的数量，pic：商品的图片
     */
    public function jmrpay(){
        $pay['sale_money'] = I('sale_money');
        $pay['out_trade_no'] = I('out_trade_no');
        $pay['subject'] = I('subject');
        $pay['terminal_id'] = I('terminal_id');
        $pay['num'] = I('num');
//        $pay['pic'] = $this->jmrapi->query_picname($pay['subject']);
        dump($pay['subject']);
        dump('34534534');
        exit;
        $openid = $_SESSION['openid'];
//        dump($pay);exit;
        if(empty($openid)){
            $openid = $this->authorize_openid();
            $_SESSION['openid'] = $openid;
        }
        $userinfo = M('user')->where('openid="'.$openid.'"')->find();
        $this->assign('pay',$pay);
        $this->assign('userinfo',$userinfo);
        $this->display();
    }
    /**
     * 确定支付
     */
    public function supay(){
        $openid = $_SESSION['openid'];
        if(empty($openid)){
            $openid = $this->authorize_openid();
            $_SESSION['openid'] = $openid;
        }
        $money = I('money');
        $out_trade_no = I('out_trade_no');
        $list = $this->jmrapi->vipsale($openid,intval($money),$out_trade_no);
        if($list['result']==0){
            //z账户的资金减少
            $userinfo = M('user')->where('openid="'.$openid.'"')->find();
            $moneyu = $userinfo['user_balance']-$money;
            M('user')->where('openid="'.$openid.'"')->save(array('user_balance'=>$moneyu));
            $this->success('支付成功');
        }else{
            $this->error('支付失败');
        }
    }
    /**
     * 支付成功
     */
    public function paysuccess(){
        $openid = $_SESSION['openid'];
        if(empty($openid)){
            $openid = $this->authorize_openid();
            $_SESSION['openid'] = $openid;
        }
        //获取用户的个人信息
        $userinfo = M('user')->where('openid="'.$openid.'"')->find();
        $this->assign('userinfo',$userinfo);
        $this->assign('terminal_id',I('terminal_id'));
        $this->assign('out_trade_no',I('out_trade_no'));
        $this->assign('money',I('money'));
        $this->display();
    }
    public function qidai(){
        $this->display();
    }
    /**
     * denglu
     */
    public function login(){
//        dump('325252352');exit;
        $this->display();
    }
    public function logins(){
        $openid = $this->authorize_openid();
        if(empty($openid)){
            $this->redirect('index');
        }
    }
    /**
     * 扫码
     */
    public function saoma(){
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
            "appId"     => 'wx97f3addd5bdc03d0',
            "nonceStr"  => $nonceStr,
            "timestamp" => $timestamp,
            "url"       => $url,
            "signature" => $signature,
            "rawString" => $string
        );
        $this->assign('signPackage',$signPackage);
        $this->display();
    }
    /**
     * 支付接口回调
     */
    public function PayCallBack(){
        $openid = $_SESSION['openid'];
        if(empty($openid)){
            $openid = $this->authorize_openid();
            $_SESSION['openid'] = $openid;
        }
        $order_id = I('order_id');
        $return = $this->jmrapi->orderid_vipsale($openid,$order_id);
        if($return['result']==0){
            return true;
        }else{
            return false;
        }
        dump($return);exit;
    }
}