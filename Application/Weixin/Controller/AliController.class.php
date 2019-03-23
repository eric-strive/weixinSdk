<?php

namespace Weixin\Controller;

use Think\Controller;

/**
 * 前台公共控制器
 * 为防止多分组Controller名称冲突，公共Controller名称统一使用分组名称
 */
class AliController extends Controller
{
    public static $is_ali_browser = false;
    public static $aliBase = '';
    public static $appId = '20180721111111';
    public static $merchantPrivateKey = '111111111111111FkyBgrp6TFYR6X8q+jp+uFtpy0oSbu/gKZYt99Jdvp3f1ivpSd4GfUjja8RESgHCMlC684fAv65VVVmYV+RMy2ZQsfdS6AG6sg3bVyjFFIBdGPwHZ7e9v2nx6G2Xy91GgLUt6h1VNHC9ZwyBbmILU9cZhPSFVqJJUy+//ssnBRxeP3UxnlM1coSw28zWYK50jE9WD6FN7zcRbuR6463ldLAp9BRlgdimRoM4tC4iEZn7ItsOMk8iMACjt2kG5S3RuvsbUix9KhXZ8cdVQ5szPYlyZognJEEkYP+hwIDAQABAoIBAQCI2PcRXGfGLIAjcfRgkdYzxtJDnIP6pdbWCtTz2mi4IEE3rvknZYVEIJ3pozkMsEJ1OZBLfLEAEs7G5Yo0Nvth4s3UPWjZCUXv4vOeRZGPtltYG4HOvcUBBtIZZ0CK74FHSx3RuV4SBvl7cdEC7yknZjRCHu+D4QbtciL2fI917B3Xqbu4o+cNyGeGEoEf0F9wktuogJtMXb5gMyBRX1oPvRYf4dmV1iXFHFwlNnk9PntZDwXF5Xnj9YO4gPSuxuSa0KGFVSAw4LEvN9pmha8fLOPAsaBkiscXsKL2D/WSp4c7UQE0ZB4Hm5qJsBwEI34EA8fFnUrbAS52HYrQXH9BAoGBAO7/hftM2AK9yeSPh+KHVJHnhUTy1050AGZjSAwc9B3M2kupbrMIaJpbzW2dOavApbnspLrg54vZL+iKeg9p++ghCMp9euI4Y/4H9dFGJAIl/gQwjX/MyIPMO4lIdEyCKXpiaWqIKQUaMec2nmhEzWjo1LiCNlkBBJPSoOr7+cAJAoGBAMyWP7ZIbRhn05bR1rcBYtLb9jf1E1kLvN97zn1FD31rF20DWiwiS3+o9tb9q0y2X+DiHS3S74qrvoOnBS/pApKOgHNapVfZbOHUG/B1OG/xNHlYJqMMI5VNN6eRwNH4kb1/jVKEPoSaU2I7qIKTqIDOhkWneDPundQw3rbUJ04PAoGANLIeZoRQ9HnINB75hRb1rJ3xonwwwNmO5QRq9FDF+nQahag8AjmOZsprBwotlxMI92+X+qRwKUOf7pPpydBCfLIb8BmuqIZqda5notbmRZr+4QmbiVwrsfOsBN721Y+1eYiNWbHf6YIXErXWQ4M4DzOXA3+iYAl7auR8GpOoQ5ECgYA+0wC7nCAMN43tKYDy77UGJU/FvTd0x7r3MdCl/TgVfnZSHo5pMRrYF+289WbOBs8IgscNFFJE1hqJ2RcpsDb/BE2DntlZUcYyzuzJeWWcUoiXnq82sHqY1X56sbJxiBBRDcM7DKsaEz3503Iarvc2nrTy3Mt3vxWuXFd380wilwKBgDg/zXcLj9ik/brwh8nbWD0CtQXWjmBsSYkb8yQXh9fuQuDfS03pmXKY/dn+tVI7LW/TBkWN6WBKPAA74O+zE1ChglWpErkrjjv6NveLvpiReBp30MFQawRtQ4ApsEmGWvX1+v17OjsS72/Z0hxaV+ncZafaXtUSU8bn4glOV8Qh';
    public static $format = 'json';
    public static $charset = 'UTF-8';
    public static $signType = 'RSA2';
    public static $apiVersion = '2.0';
    public static $gatewayUrl = 'https://openapi.alipay.com/gateway.do';
    public static $code = 'code';
    public static $alipayPublicKey = '11111111111111111113SMxv4hV2MQmlojNByzZFn3YREj8QH4b44Xxjzr/yPL+SnLhDck8Fh9HXh7RaTAGAFkRuVzlYIO/zQdWg/kt5ahS3MpRgWY/9/B3rLNJoPoHA6XrlYGJ2/W5kNjxBgcve01obfkb35J5oa8f8R0hLacPU2vc3NGis2mW8QnEGMyREqQJL9o6u+W4vzH3ex8nXx+0ERXHm3RXNZ3b72q34j2ZODLNo+fORTU0KcvHP0Fg+/0M/UPXTcvzbd0Tms3S0s0zZU/mSINw0rEo7CD+6q3JzDuijbWWpBQP7BqX6+awa+bURUe5z5XtDTObi7wZTpHywuw5Nu33b5Ecp1LOQIDAQAB';
    public static $returnUrl = 'http://xxxxxxxx/jjw/Home/Index/pay_success';
    public static $notifyUrl = '';

    protected function _initialize()
    {
        Vendor('Alipay.aop.SignData');
        Vendor('Alipay.aop.AopClient');
        self::$aliBase = new \AopClient();
        self::$aliBase->gatewayUrl = "https://openapi.alipay.com/gateway.do";
        self::$aliBase->appId = self::$appId;
        self::$aliBase->rsaPrivateKey = self::$merchantPrivateKey;
        self::$aliBase->format = self::$format;
        self::$aliBase->charset = self::$charset;
        self::$aliBase->signType = self::$signType;
        self::$aliBase->apiVersion = self::$apiVersion;
        self::$aliBase->alipayrsaPublicKey = self::$alipayPublicKey;
    }

    /**
     * 支付宝手机支付
     * @param $out_trade_no
     * @param $proName
     * @param $total_amount
     * @param $body
     * @return mixed
     */
    public function wap_pay()
    {
        $remark = I('remark', trim());
        $total_fee = I('total_fee');
        $body = I('body');
        $out_trade_no = uniqid() . time();
        Vendor('Alipay.aop.request.AlipayTradeWapPayRequest');
        $request = new \AlipayTradeWapPayRequest();
        $request->setReturnUrl(self::$returnUrl);
        $request->setNotifyUrl(self::$notifyUrl);
        $request->setBizContent("{" .
            "    \"product_code\":\"QUICK_WAP_WAY\"," .
            "    \"subject\":\"$remark\"," .
            "    \"out_trade_no\":\"$out_trade_no\"," .
            "    \"total_amount\":$total_fee," .
            "    \"body\":\"$body\"" .
            "  }");
        $result = self::$aliBase->pageExecute($request);
        $data = array(
            'store_id' => I('store_id'),
            'consume_money' => I('total_fee', trim()),
            'uid' => $_SESSION['uid'],
            'consume_time' => time(),
            'remark' => $remark,
            'out_trade_no' => $out_trade_no,
            'type' => 2,
            'status' => 0,
        );
        M('jjw_consume_list')->add($data);
        //输出
        $this->success($result);
    }

    public function authorizeUserInfo()
    {

    }

    public static function getAccessToken($auth_code)
    {
        Vendor('Alipay.aop.request.AlipaySystemOauthTokenRequest');
        $request = new \AlipaySystemOauthTokenRequest ();
        $request->setGrantType("authorization_code");
        $request->setCode($auth_code);
        $result = self::$aliBase->execute($request);
        $responseNode = str_replace(".", "_", $request->getApiMethodName()) . "_response";
        $data = [
            'access_token' => $result->$responseNode->access_token,
            'refresh_token' => $result->$responseNode->refresh_token,
            'user_id' => $result->$responseNode->user_id,
            'alipay_user_id' => $result->$responseNode->alipay_user_id,
        ];
        return $data;
    }

    public function userInfoAuth($scope = 'auth_base', $state = 'state')
    {
        if (!$_GET['auth_code']) {
            $appId = self::$appId;
            $redirect_url = urlencode('http://myjjfww.com/jjw/Home/Ali/userInfoAuth');
            $auth_url = "https://openauth.alipay.com/oauth2/publicAppAuthorize.htm?app_id={$appId}&scope={$scope}&redirect_uri={$redirect_url}&state={$state}";
            redirect($auth_url);
            exit;
        } else {
            $data = self::getAccessToken($_GET['auth_code']);
            //判断数据库是否存该信息
            $alipayModel = M('alipay_user_info');
            $is_exist = $alipayModel->where('alipay_mark_user_id="' . $data['alipay_user_id'] . '"')->find();
            if (empty($is_exist)) {
                $alipayUserinfo = [
                    'alipay_mark_id' => $data['user_id'],
                    'alipay_mark_user_id' => $data['alipay_user_id'],
                    'add_time' => time(),
                ];
                $alipay_user_id = $alipayModel->add($alipayUserinfo);
                $_SESSION['alipay_user_id'] = $alipay_user_id;
            }
            $_SESSION['alipay_user_id'] = $is_exist['alipay_user_id'];
            $_SESSION['alipay_mark_id'] = $data['alipay_user_id'];
            if ($_SESSION['register-jump-url']) {
                redirect($_SESSION['register-jump-url']);
            }
            return $data['alipay_user_id'];
        }
    }
}
