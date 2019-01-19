<?php

namespace Weixin\Controller;

use Home\ORG\weixin\templateNews;
use Think\Controller;
use Home\Controller\AliController;

/**
 * 前台公共控制器
 * 为防止多分组Controller名称冲突，公共Controller名称统一使用分组名称
 */
class HomeController extends Controller
{

    /**
     * 接口名称与URL映射
     *
     * @var array
     */
    public $is_wexin_browser = false;
    public $is_alipay_browser = false;
    protected static $url = [//自动授权的微信地址
        'oauth_authorize' => 'https://open.weixin.qq.com/connect/oauth2/authorize',
        //获取Access Token的微信地址（可以调用的基础）
        'oauth_user_token' => 'https://api.weixin.qq.com/sns/oauth2/access_token',
        //获取用户信息的微信地址，获取用户信必须先授权；
        'oauth_get_userinfo' => 'https://api.weixin.qq.com/sns/userinfo',
        //获取全部关注用户
        'user_get' => 'https://api.weixin.qq.com/cgi-bin/user/get',
        // 获取用户信息
        'user_info' => 'https://api.weixin.qq.com/cgi-bin/user/info',
        'user_info_batch' => 'https://api.weixin.qq.com/cgi-bin/user/info/batchget',
        //设置用户备注名
        'user_remark' => 'https://api.weixin.qq.com/cgi-bin/user/info/updateremark',
        'user_in_group' => 'https://api.weixin.qq.com/cgi-bin/groups/getid',
        'user_to_group' => 'https://api.weixin.qq.com/cgi-bin/groups/members/update',
        'batch_to_group' => 'https://api.weixin.qq.com/cgi-bin/groups/members/batchupdate',
        'access_token' => 'https://api.weixin.qq.com/cgi-bin/token', // 获取ACCESS_TOKEN
    ];

    protected function _initialize()
    {
        //免登设置，只有在微信里面才进行这些操作
        //根据appid查询配置
        $this->weiconfig = [
            'appid' => C('WX_CONFIG')['appid'],
            'appsecret' => C('WX_CONFIG')['appsecret'],
        ];
        if (strpos($_SERVER['HTTP_USER_AGENT'], 'MicroMessenger') !== false) {
            $this->is_wexin_browser = true;
        } elseif (strpos($_SERVER['HTTP_USER_AGENT'], 'AlipayClient') !== false) {
            $this->is_alipay_browser = true;
        }
        $_SESSION['openid'] = 'o9S510tf6Yl89crT7aaSApq9JXnw';
        if ($this->is_wexin_browser && empty($_SESSION['openid'])) {
            $p_referral_code = I('p_referral_code');
            $p_referral_code = $p_referral_code ? $p_referral_code : 'STATE';
            $_SESSION['openid'] = $openid = $this->authorize_openid('snsapi_userinfo', $p_referral_code);
        } elseif ($this->is_alipay_browser && empty($_SESSION['alipay_mark_id'])) {
//            $_SESSION['register-jump-url'] = 'http://'.$_SERVER['HTTP_HOST'].$_SERVER['REQUEST_URI'];
//            $this->redirect('Home/Ali/userInfoAuth');
        } else {
            $openid = $_SESSION['openid'];
        }
        if (($_SESSION['openid'])) {
            if (empty($_SESSION['userinfo']) || empty($_SESSION['uid'])) {
                $rs = M("user")->where('openid="' . $openid . '"')->find();
                $_SESSION['uid'] = $rs['uid'];
                //判断推荐码是否为空
                if (empty($rs['referral_code'])) {
                    $rs['referral_code'] = $referral_code = make_coupon_card(9);
                    M("user")->where('openid="' . $openid . '"')->setField('referral_code', $referral_code);
                }
                $_SESSION['userinfo'] = $rs;
            }
            //判断是否需要注册
            $not_register = C('NOT_REGISTER');
            $mac = MODULE_NAME . '_' . CONTROLLER_NAME . '_' . ACTION_NAME;
            if (!in_array(MODULE_NAME, $not_register) && !in_array($mac, $not_register)) {
                if (empty($_SESSION['u_id'])) {
                    //判断其有没有注册
                    $jjw_userinfo = M("jjwUserinfo")->where('uid=' . $_SESSION['uid'])->find();
                    $_SESSION['u_id'] = $jjw_userinfo['id'];
                    if (empty($jjw_userinfo)) {
                        //记住上次进来的链接
                        $_SESSION['register-jump-url'] = 'http://' . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'];
                        $this->redirect('Home/Index/register');
                    } else {
                        $_SESSION['u_id'] = $jjw_userinfo['id'];
                    }
                }
            }
        }
        if ($_SESSION['alipay_user_id'] && !$_SESSION['u_id']) {
            $jjw_userinfo = M("jjwUserinfo")->where('alipay_user_id=' . $_SESSION['alipay_user_id'])->find();
            if ($jjw_userinfo) {
                $_SESSION['u_id'] = $jjw_userinfo['id'];
            }
        }
    }

    /**
     * OAuth 用户同意授权，获取code
     *
     * @param  [type] $callback 回调URI，填写完整地址，带http://
     * @param  string $state 重定向后会带上state参数，开发者可以填写a-zA-Z0-9的参数值
     * @param  string $scope snsapi_userinfo 获取用户授权信息，snsapi_base直接返回openid
     *                       1、以snsapi_base为scope发起的网页授权，是用来获取进入页面的用户的openid的，并且是静默授权并自动跳转到回调页的。用户感知的就是直接进入了回调页（往往是业务页面）
     *                       2、以snsapi_userinfo为scope发起的网页授权，是用来获取用户的基本信息的。但这种授权需要用户手动同意，并且由于用户同意过，所以无须关注，就可在授权后获取该用户的基本信息。
     *                       3、用户管理类接口中的“获取用户基本信息接口”，是在用户和公众号产生消息交互或关注后事件推送后，才能根据用户OpenID来获取用户基本信息。这个接口，包括其他微信接口，都是需要该用户（即openid）关注了公众号后，才能调用成功的。
     *
     * @return string
     */
    public static function url($callback, $state = 'STATE', $scope = 'snsapi_userinfo')
    {
        $params = [
            'appid' => C('WX_CONFIG')['appid'],
            'redirect_uri' => $callback,
            'response_type' => 'code',
            'scope' => $scope,
            'state' => $state,
        ];

        return self::$url['oauth_authorize'] . '?' . http_build_query($params) . '#wechat_redirect';
    }

    /**
     * 根据openid自动登录
     */
    public function authorize_openid($scope = 'snsapi_userinfo', $state = 'STATE')
    {
        if (empty($_GET['code']) || empty($_SESSION['weixin']['state'])) {
            //只获取用户的openid
            $state = $state ? $state : 'STATE';
            $_SESSION['weixin']['state'] = $state;
            $params = [
                'appid' => $this->weiconfig['appid'],
                'redirect_uri' => preg_replace('#&code=(\\w+)#', '', 'http://myjjfww.com' . $_SERVER['REQUEST_URI']),
                'response_type' => 'code',
                'scope' => $scope,
                'state' => $state,
            ];
            $oauthUrl = 'https://open.weixin.qq.com/connect/oauth2/authorize?' .
                http_build_query($params) . '#wechat_redirect';
            redirect($oauthUrl);
            exit();
        } else {
            if (isset($_GET['code']) && isset($_GET['state']) && ($_GET['state'] == $_SESSION['weixin']['state'])) {
                unset($_SESSION['weixin']);

                import('Home.ORG.weixin.Utils');
                //通过code获取Access Token
                $code = isset($_GET['code']) ? $_GET['code'] : '';
                if (!$code) {
                    dump('未获取到CODE信息');
                    exit;
                }
                $params = [
                    'appid' => $this->weiconfig['appid'],
                    'secret' => $this->weiconfig['appsecret'],
                    'code' => $code,
                    'grant_type' => 'authorization_code',
                ];
                $return = \Home\ORG\weixin\Utils::api('https://api.weixin.qq.com/sns/oauth2/access_token', $params);
                if ($return['errcode']) {
                    $error = \Home\ORG\weixin\Utils::parseError($return);
                    dump($error);
                    exit;
                }
                //获取到openid后
                if ($scope != 'snsapi_userinfo') {
                    $_SESSION['openid'] = $return['openid'];

                    return $return['openid'];
                }
                if ($return['openid']) {
                    $_SESSION['openid'] = $return['openid'];
                    //判断信息是否保存
                    $openid = $return['openid'];
                    $rs = M("user")->where('openid="' . $openid . '"')->find();
                    if (!$rs) {
                        $userinfo = $this->getUserInfo1($return['openid'], $return['access_token']);
                        $userinfo['addtime'] = time();
                        $userinfo['referral_code'] = make_coupon_card(9);
                        //判断推荐的人,添加推荐
                        if ($_GET['state'] != 'STATE') {
                            $p_referral_code = $_GET['state'];
                            $pfinfo = M("user")
                                ->where('referral_code="' . $p_referral_code . '"')
                                ->find();
                            $userinfo['stair_id'] = $pfinfo['uid'] ? $pfinfo['uid'] : 0;
                            $userinfo['second_id'] = $pfinfo['stair_id'] ? $pfinfo['stair_id'] : 0;
                            $userinfo['three_id'] = $pfinfo['second_id'] ? $pfinfo['second_id'] : 0;
                        }
                        $we = M('user')->add($userinfo);
                        $file = $this->ticket($we);
                        M('user')->where('openid= "' . $openid . '"')->setField('img_url', $file["url"]);
                        M('user')->where('openid= "' . $openid . '"')->setField('img', $file["filename"]);
                    }

                    return $openid;
                } else {
                    //授权获取openid
                    $this->error("获取用户信息失败");
                }
            } else {
                //直接跳到主页
                $this->error("访问出错");
            }
        }
    }

    /**
     * 自动登录
     * 手机号、union_id、open_id
     * 直接登录入口
     **/
    public function autologin($openid)
    {
        $rs = M("user")->where('openid="' . $openid . '"')->find();
        $_SESSION['uid'] = $rs['uid'];
        $_SESSION['jmr_id'] = $rs['jmr_id'];
        $_SESSION['openid'] = $openid;
        $_SESSION['userinfo'] = $rs;
    }

    /**
     * @param $openid
     *
     * @return bool|mixed
     * 判断是否关注
     */
    public function attention($openid)
    {
        import('Home.ORG.weixin.Utils');
        $access_token = $this->getToken();
        if ($openid == "") {
            if (empty($_SESSION['openid'])) {
                $openid = $this->authorize_openid();
            } else {
                $openid = $_SESSION['openid'];
            }
        }
        if ($access_token) {
            $return = Utils::userinfo($openid, $access_token);

            return $return;
        } else {
            dump("获取token信息失败！");
            exit;
        }
    }

    /**
     * @param      $url
     * @param null $data
     *
     * @return mixed
     * 获取用户信息
     */
    function getUserInfo1($openid, $access_token)
    {
        //        $access_token = $this->getToken();
        $url = "https://api.weixin.qq.com/sns/userinfo?access_token={$access_token}&openid={$openid}&lang=zh_CN";
        $result = $this->https_request($url);
        $user = json_decode($result, true);
        $_SESSION['userinfo'] = $user;

        return $user;
    }

    function getUserInfo($openid)
    {
        $access_token = $this->getToken();
        $url = "https://api.weixin.qq.com/cgi-bin/user/info?access_token={$access_token}&openid={$openid}&lang=zh_CN";
        $result = $this->https_request($url);
        $user = json_decode($result, true);

        return $user;
    }

    //CURL请求的函数http_request()
    function https_request($url, $data = null)
    {
        $curl = curl_init();
        curl_setopt($curl, CURLOPT_URL, $url);
        curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, false);
        if (!empty($data)) {
            curl_setopt($curl, CURLOPT_POST, 1);
            curl_setopt($curl, CURLOPT_POSTFIELDS, $data);
        }
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
        $output = curl_exec($curl);
        curl_close($curl);

        return $output;
    }

    /**
     * 自动获取用户的token
     *
     * 2016.5.20
     */
    public static function getToken()
    {
        import("@.ORG.weixin.Utils");
        //        //根据appid查询配置
        $appid = C('WX_CONFIG')['appid'];
        $weifig = M('wx_config')->where('appid="' . $appid . '"')->find();
        $time = intval(time());
        $wtime = intval($weifig['attime']);
        if ($wtime > $time) {
            return $weifig['access_token'];
        }
        $params = [
            'appid' => C('WX_CONFIG')['appid'],
            'secret' => C('WX_CONFIG')['appsecret'],
            'grant_type' => 'client_credential',
        ];
        $result = \Home\ORG\weixin\Utils::api(self::$url['access_token'], $params);
        if ($result) {
            $data['access_token'] = $result['access_token'];
            $data['attime'] = intval(time()) + 5000;
            M('wx_config')->where('appid="' . $appid . '"')->save($data);

            return $result['access_token'];
        } else {
            return false;
        }
    }

    /* 空操作，用于输出404页面 */
    public function _empty()
    {
        $this->redirect('Index/index');
    }

    public function createNonceStr($length = 16)
    {
        $chars = "abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789";
        $str = "";
        for ($i = 0; $i < $length; $i++) {
            $str .= substr($chars, mt_rand(0, strlen($chars) - 1), 1);
        }

        return $str;
    }

    public function getJsApiTicket()
    {
        $accessToken = $this->getToken();
        $url = "https://api.weixin.qq.com/cgi-bin/ticket/getticket?type=jsapi&access_token=$accessToken";
        $res = json_decode($this->https_request($url));
        $ticket = $res->ticket;

        return $ticket;
    }

    /**
     * 微信JSAPI需要的数据
     * @return array
     */
    public function getSignPackage()
    {
        $jsapiTicket = $this->getJsApiTicket();
        $protocol = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off' || $_SERVER['SERVER_PORT'] == 443) ?
            "https://" : "http://";
        $url = "$protocol$_SERVER[HTTP_HOST]$_SERVER[REQUEST_URI]";
        $timestamp = time();
        $nonceStr = $this->createNonceStr();
        $string = "jsapi_ticket=$jsapiTicket&noncestr=$nonceStr&timestamp=$timestamp&url=$url";
        $signature = sha1($string);
        $signPackage = [
            "appId" => C('WX_CONFIG')['appid'],
            "nonceStr" => $nonceStr,
            "timestamp" => $timestamp,
            "url" => $url,
            "signature" => $signature,
            "rawString" => $string,
        ];

        return $signPackage;
    }

    /**
     * 获取二维码
     * 推荐人id
     */
    public function ticket($rid = "")
    {
        if ($rid == "") {
            $rid = I('rid');
        }
        $name = $rid . ".jpg";
        $filename = "./Uploads/ticket/" . $name;
        $access_token = $this->getToken();
        $url = "https://api.weixin.qq.com/cgi-bin/qrcode/create?access_token={$access_token}";
        $jsonstr = '{"action_name": "QR_LIMIT_SCENE", "action_info": {"scene": {"scene_id": ' . $rid . '}}}';
        $result = $this->https_request($url, $jsonstr);
        $arr = json_decode($result, true);
        $ticket = $arr['ticket'];
        $url = "https://mp.weixin.qq.com/cgi-bin/showqrcode?ticket=" . urlencode($ticket);
        $imageInfo = $this->downImage($url);
        if (file_exists($filename)) {
            $file['filename'] = "/Uploads/ticket/" . $name;
            $file['url'] = $arr['url'];

            return $file;
        } else {
            $fi = file_put_contents($filename, $imageInfo);
            if ($fi) {
                $file['filename'] = "/Uploads/ticket/" . $name;
                $file['url'] = $arr['url'];

                return $file;
            }
        }

    }

    /**
     * 生成二维码图片
     * @param $url
     * @return mixed
     */
    public function downImage($url)
    {
        $curl = curl_init($url);
        curl_setopt($curl, CURLOPT_HEADER, 0);
        curl_setopt($curl, CURLOPT_NOBODY, 0);
        curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, false);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
        $output = curl_exec($curl);
        curl_close($curl);

        return $output;
    }

    /**
     * 发送模版消息，不同的模版需要的参数不一样
     * @param $userinfo
     * @param $out_trade_no
     */
    public function sendTemMsg($userinfo, $out_trade_no)
    {
        $tem = new templateNews(C('WX_CONFIG')['appid'], C('WX_CONFIG')['appsecret']);
        $token = $this->getToken();
        $tempKey = 'OPENTM201743389';
        $color = "#000";
        $time = date("Y-m-d H:i:s");
        $dataArr = array(
            'openid' => $userinfo['openId'],
            'href' => C('BASE_URL_jjw') . '/Home/Usercenter/information?store_id=' . $orderInfo['store_id'],
            'first' => '您好，您有新的订单啦。',
            'keyword1' => $out_trade_no,
            'keyword2' => $orderInfo['name'] . '（' . $time . '已支付' . $total_fee . '元）',
            'keyword3' => $userinfo['realname'],
            'keyword4' => $userinfo['mobile'],
            'keyword5' => '到店自取',
            'remark' => '您可以点击进入个人中心，感谢您的使用。'
        );
        $we = $tem->sendTempMsg($tempKey, $dataArr, $token, $color);
    }
}
