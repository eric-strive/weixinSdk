<?php
namespace Weixin\Controller;
use Think\Controller;
class CommonController extends Controller
{
    public $token = '';
    public $wecha_id = '';
    public $is_wexin_browser = false;
    public $weiconfig = "";//微信的配置

    /**
     * 接口名称与URL映射
     * @var array
     * 2016.5.18
     * 汪威
     */
    protected static $url = array(//自动授权的微信地址
        'oauth_authorize'    => 'https://open.weixin.qq.com/connect/oauth2/authorize',
        //获取Access Token的微信地址（可以调用的基础）
        'oauth_user_token'   => 'https://api.weixin.qq.com/sns/oauth2/access_token',
        //获取用户信息的微信地址，获取用户信必须先授权；
        'oauth_get_userinfo' => 'https://api.weixin.qq.com/sns/userinfo',
        //获取全部关注用户
        'user_get'        => 'https://api.weixin.qq.com/cgi-bin/user/get',
        // 获取用户信息
        'user_info'       => 'https://api.weixin.qq.com/cgi-bin/user/info',
        'user_info_batch' => 'https://api.weixin.qq.com/cgi-bin/user/info/batchget',
        //设置用户备注名
        'user_remark'     => 'https://api.weixin.qq.com/cgi-bin/user/info/updateremark',
        'user_in_group'   => 'https://api.weixin.qq.com/cgi-bin/groups/getid',
        'user_to_group'   => 'https://api.weixin.qq.com/cgi-bin/groups/members/update',
        'batch_to_group'  => 'https://api.weixin.qq.com/cgi-bin/groups/members/batchupdate',
        'access_token' => 'https://api.weixin.qq.com/cgi-bin/token', // 获取ACCESS_TOKEN
    );
    public function __construct()
    {
        parent::__construct();
        //根据appid查询配置
        $this->weiconfig = array(
            'appid'=>C('WX_CONFIG')['appid'],
            'appsecret'=>C('WX_CONFIG')['appsecret'],
        );
        //判断是否在微信里面访问
//        if (strpos($_SERVER['HTTP_USER_AGENT'], 'MicroMessenger') !== false) {
//            $this->is_wexin_browser = true;
//        }
    }
    /**
     * @param $url
     * @param null $data
     * @return mixed
     * 获取用户信息
     */
    function getUserInfo($openid) {
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
        curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, FALSE);
        curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, FALSE);
        if (!empty($data)){
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
        $weifig = M('wx_config')->where('appid="'.$appid.'"')->find();
        $time = intval(time());
        $wtime = intval($weifig['attime']);
        if($wtime>$time){
            return $weifig['access_token'];
        }
        $params = array(
            'appid'      =>C('WX_CONFIG')['appid'],
            'secret'     => C('WX_CONFIG')['appsecret'],
            'grant_type' => 'client_credential',
        );
        $result = \Home\ORG\weixin\Utils::api(self::$url['access_token'], $params);
        if ($result) {
            $data['access_token'] = $result['access_token'];
            $data['attime'] = intval(time())+5000;
            M('wx_config')->where('appid="'.$appid.'"')->save($data);
            return $result['access_token'];
        } else {
            return false;
        }
    }

    /**
     * 用户分组
     */
    function groupuser($openid, $groupid=0, $subscribe=true) {
        //如果参数subscribe=true就移到分组，否则只在本数地加个用户
        if($subscribe) {
            $access_token = $this->getToken();
            //接口是移动组的接口， 如果关注时，用指定组的能数，直接将用户分到指定的组中
            $url = "https://api.weixin.qq.com/cgi-bin/groups/members/update?access_token={$access_token}";
            //参数post json
            $jsonstr = '{"openid":"'.$openid.'","to_groupid":'.$groupid.'}';
            $result = $this->https_request($url, $jsonstr);
        }
    }
    /**
     * 获取二维码
     * 推荐人id
     */
    public function ticket($rid=""){
        if($rid==""){
            $rid = I('rid');
        }
        $name = $rid.".jpg";
        $filename = "./Uploads/ticket/".$name;
        $access_token = $this->getToken();
        $url = "https://api.weixin.qq.com/cgi-bin/qrcode/create?access_token={$access_token}";
        $jsonstr = '{"action_name": "QR_LIMIT_SCENE", "action_info": {"scene": {"scene_id": '.$rid.'}}}';
        $result = $this->https_request($url, $jsonstr);
        $arr = json_decode($result, true);
        $ticket = $arr['ticket'];
        $url = "https://mp.weixin.qq.com/cgi-bin/showqrcode?ticket=".urlencode($ticket);
        $imageInfo = $this->downImage($url);
        if (file_exists($filename)) {
            $file['filename'] = "/Uploads/ticket/".$name;
            $file['url'] = $arr['url'];
            return $file;
        } else {
            $fi = file_put_contents($filename, $imageInfo);
            if($fi){
                $file['filename'] = "/Uploads/ticket/".$name;
                $file['url'] = $arr['url'];
                return $file;
            }        }

    }
    public function downImage($url) {
        $curl = curl_init($url);
        curl_setopt($curl, CURLOPT_HEADER, 0);
        curl_setopt($curl, CURLOPT_NOBODY, 0);
        curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, FALSE);
        curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, FALSE);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
        $output = curl_exec($curl);
        curl_close($curl);
        return $output;
    }
}

?>
