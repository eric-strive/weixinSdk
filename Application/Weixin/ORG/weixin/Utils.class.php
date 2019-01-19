<?php
// +------------------------------------------------+
// |http://www.jiurong.com                           |
// +------------------------------------------------+
// | 微信类 |
// +------------------------------------------------+
// | Author: nicai <>                  |
// +------------------------------------------------+

/**
 *
 */
namespace Home\ORG\weixin;
class Wechat
{
    /**
     * 保存错误信息
     * @var string
     */
    protected static $error = '';

    /**
     * 默认的配置参数
     * @var array
     */
    protected static $configs = array(
        'token'        => 'weiixn',
        'appid'        => 'wx1557579a81822b6d',
        'secret'       => '4619b639e6ebc395061239484926199b',
        'access_token' => 'weixin',
        'encode'       => false,
        'AESKey'       => '',
        'mch_id'       => '',
        'paykey'       => '',
        'pem'          => '',
    );

    /**
     * 传入初始化参数
     * 接受消息,如果是加密的需要传入一些参数,否则用不到
     */
    public function __construct($configs = array())
    {
        if (!empty($configs) && is_array($configs)) {
            self::$configs = array_merge(self::$configs, $configs);
        }
    }

    /**
     * 初始化
     * @param  array   $configs
     * @param  boolean $force 强制初始化
     * @return [type]
     */
    public static function init($configs = array(), $force = false)
    {
        static $wechat;
        if (is_null($wechat) || $force == true) {
            $wechat = new Wechat($configs);
        }
        return $wechat;
    }

    /**
     * 验证URL有效性,校验请求签名
     * @return string|boolean
     */
    public static function valid()
    {
        $echoStr = isset($_GET["echostr"]) ? $_GET["echostr"] : '';
        if ($echoStr) {
            self::checkSignature() && exit($echoStr);
        } else {
            !self::checkSignature() && exit('Access Denied!');
        }
        return true;
    }

    /**
     * 检查请求URL签名
     * @return boolean
     */
    private static function checkSignature()
    {
        $signature = isset($_GET['signature']) ? $_GET['signature'] : '';
        $timestamp = isset($_GET['timestamp']) ? $_GET['timestamp'] : '';
        $nonce     = isset($_GET['nonce']) ? $_GET['nonce'] : '';
        if (empty($signature) || empty($timestamp) || empty($nonce)) {
            return false;
        }
        $token  = self::$configs['token'];
        $tmpArr = array($token, $timestamp, $nonce);
        sort($tmpArr, SORT_STRING);
        $tmpStr = implode($tmpArr);
        return sha1($tmpStr) == $signature;
    }

    /**
     * 返回错误信息
     * @return string
     */
    public static function errors($msg = null)
    {
        if (!is_null($msg)) {
            self::$error = $msg;
        } else {
            return self::$error;
        }
    }
}
class Utils extends Wechat
{
    /**
     * 错误信息
     * @var array
     */
    private static $errMsg = array(
        -1      => '系统繁忙，此时请开发者稍候再试',
        0       => '请求成功',
        40001   => '获取access_token时AppSecret错误，或者access_token无效。请开发者认真比对AppSecret的正确性，或查看是否正在为恰当的公众号调用接口',
        40002   => '不合法的凭证类型',
        40003   => '不合法的OpenID，请开发者确认OpenID（该用户）是否已关注公众号，或是否是其他公众号的OpenID',
        40004   => '不合法的媒体文件类型',
        40005   => '不合法的文件类型',
        40006   => '不合法的文件大小',
        40007   => '不合法的媒体文件id',
        40008   => '不合法的消息类型',
        40009   => '不合法的图片文件大小',
        40010   => '不合法的语音文件大小',
        40011   => '不合法的视频文件大小',
        40012   => '不合法的缩略图文件大小',
        40013   => '不合法的AppID，请开发者检查AppID的正确性，避免异常字符，注意大小写',
        40014   => '不合法的access_token，请开发者认真比对access_token的有效性（如是否过期），或查看是否正在为恰当的公众号调用接口',
        40015   => '不合法的菜单类型',
        40016   => '不合法的按钮个数',
        40017   => '不合法的按钮个数',
        40018   => '不合法的按钮名字长度',
        40019   => '不合法的按钮KEY长度',
        40020   => '不合法的按钮URL长度',
        40021   => '不合法的菜单版本号',
        40022   => '不合法的子菜单级数',
        40023   => '不合法的子菜单按钮个数',
        40024   => '不合法的子菜单按钮类型',
        40025   => '不合法的子菜单按钮名字长度',
        40026   => '不合法的子菜单按钮KEY长度',
        40027   => '不合法的子菜单按钮URL长度',
        40028   => '不合法的自定义菜单使用用户',
        40029   => '不合法的oauth_code',
        40030   => '不合法的refresh_token',
        40031   => '不合法的openid列表',
        40032   => '不合法的openid列表长度',
        40033   => '不合法的请求字符，不能包含\uxxxx格式的字符',
        40035   => '不合法的参数',
        40038   => '不合法的请求格式',
        40039   => '不合法的URL长度',
        40050   => '不合法的分组id',
        40051   => '分组名字不合法',
        40117   => '分组名字不合法',
        40118   => 'media_id大小不合法',
        40119   => 'button类型错误',
        40120   => 'button类型错误',
        40121   => '不合法的media_id类型',
        40132   => '微信号不合法',
        40137   => '不支持的图片格式',
        41001   => '缺少access_token参数',
        41002   => '缺少appid参数',
        41003   => '缺少refresh_token参数',
        41004   => '缺少secret参数',
        41005   => '缺少多媒体文件数据',
        41006   => '缺少media_id参数',
        41007   => '缺少子菜单数据',
        41008   => '缺少oauth code',
        41009   => '缺少openid',
        42001   => 'access_token超时，请检查access_token的有效期',
        42002   => 'refresh_token超时',
        42003   => 'oauth_code超时',
        42007   => '用户修改微信密码，accesstoken和refreshtoken失效，需要重新授权',
        43001   => '需要GET请求',
        43002   => '需要POST请求',
        43003   => '需要HTTPS请求',
        43004   => '需要接收者关注',
        43005   => '需要好友关系',
        44001   => '多媒体文件为空',
        44002   => 'POST的数据包为空',
        44003   => '图文消息内容为空',
        44004   => '文本消息内容为空',
        45001   => '多媒体文件大小超过限制',
        45002   => '消息内容超过限制',
        45003   => '标题字段超过限制',
        45004   => '描述字段超过限制',
        45005   => '链接字段超过限制',
        45006   => '图片链接字段超过限制',
        45007   => '语音播放时间超过限制',
        45008   => '图文消息超过限制',
        45009   => '接口调用超过限制',
        45010   => '创建菜单个数超过限制',
        45015   => '回复时间超过限制',
        45016   => '系统分组，不允许修改',
        45017   => '分组名字过长',
        45018   => '分组数量超过上限',
        45047   => '客服接口下行条数超过上限',
        46001   => '不存在媒体数据',
        46002   => '不存在的菜单版本',
        46003   => '不存在的菜单数据',
        46004   => '不存在的用户',
        47001   => '解析JSON/XML内容错误',
        48001   => 'api功能未授权，请确认公众号已获得该接口，可以在公众平台官网-开发者中心页中查看接口权限',
        48004   => 'api接口被封禁，请登录mp.weixin.qq.com查看详情',
        48005   => 'api禁止删除被自动回复和自定义菜单引用的素材',
        48006   => 'api禁止清零调用次数，因为清零次数达到上限',
        50001   => '用户未授权该api',
        50002   => '用户受限，可能是违规后接口被封禁',
        61450   => '客服系统错误',
        61451   => '参数错误',
        61452   => '无效客服账号',
        61453   => '客服帐号已存在',
        61454   => '客服帐号名长度超过限制(仅允许10个英文字符，不包括@及@后的公众号的微信号)',
        61455   => '客服帐号名包含非法字符(仅允许英文+数字)',
        61456   => '客服帐号个数超过限制(10个客服账号)',
        61457   => '无效头像文件类型',
        61458   => '客户正在被其他客服接待',
        61459   => '客服不在线',
        61500   => '日期格式错误',
        65301   => '不存在此menuid对应的个性化菜单',
        65302   => '没有相应的用户',
        65303   => '没有默认菜单，不能创建个性化菜单',
        65304   => 'MatchRule信息为空',
        65305   => '个性化菜单数量受限',
        65306   => '不支持个性化菜单的帐号',
        65307   => '个性化菜单信息为空',
        65308   => '包含没有响应类型的button',
        65309   => '个性化菜单开关处于关闭状态',
        65310   => '填写了省份或城市信息，国家信息不能为空',
        65311   => '填写了城市信息，省份信息不能为空',
        65312   => '不合法的国家信息',
        65313   => '不合法的省份信息',
        65314   => '不合法的城市信息',
        65316   => '该公众号的菜单设置了过多的域名外跳（最多跳转到3个域名的链接）',
        65317   => '不合法的URL',
        9001001 => 'POST数据参数不合法',
        9001002 => '远端服务不可用',
        9001003 => 'Ticket不合法',
        9001004 => '获取摇周边用户信息失败',
        9001005 => '获取商户信息失败',
        9001006 => '获取OpenID失败',
        9001007 => '上传文件缺失',
        9001008 => '上传素材的文件类型不合法',
        9001009 => '上传素材的文件尺寸不合法',
        9001010 => '上传失败',
        9001020 => '帐号不合法',
        9001021 => '已有设备激活率低于50%，不能新增设备',
        9001022 => '设备申请数不合法，必须为大于0的数字',
        9001023 => '已存在审核中的设备ID申请',
        9001024 => '一次查询设备ID数量不能超过50',
        9001025 => '设备ID不合法',
        9001026 => '页面ID不合法',
        9001027 => '页面参数不合法',
        9001028 => '一次删除页面ID数量不能超过10',
        9001029 => '页面已应用在设备中，请先解除应用关系再删除',
        9001030 => '一次查询页面ID数量不能超过50',
        9001031 => '时间区间不合法',
        9001032 => '保存设备与页面的绑定关系参数错误',
        9001033 => '门店ID不合法',
        9001034 => '设备备注信息过长',
        9001035 => '设备申请参数不合法',
        9001036 => '查询起始值begin不合法',
    );
    /**
     * 接口名称与URL映射
     * @var array
     * 2016.5.18
     * 汪威
     */
    protected static $urlf = array(//自动授权的微信地址
        'oauth_authorize'    => 'https://open.weixin.qq.com/connect/oauth2/authorize',
        //获取Access Token的微信地址（可以调用的基础）
        'oauth_user_token'   => 'https://api.weixin.qq.com/sns/oauth2/access_token',
        //获取用户信息的微信地址，获取用户信必须先授权；
        'oauth_get_userinfo' => 'https://api.weixin.qq.com/sns/userinfo',
        //获取全部关注用户
        'user_get'        => 'https://api.weixin.qq.com/cgi-bin/user/get',
        // 获取用户信息
        'user_info'       => 'https://api.weixin.qq.com/cgi-bin/user/info',
        //
        'user_info_batch' => 'https://api.weixin.qq.com/cgi-bin/user/info/batchget',
        //设置用户备注名
        'user_remark'     => 'https://api.weixin.qq.com/cgi-bin/user/info/updateremark',
        'user_in_group'   => 'https://api.weixin.qq.com/cgi-bin/groups/getid',
        'user_to_group'   => 'https://api.weixin.qq.com/cgi-bin/groups/members/update',
        'batch_to_group'  => 'https://api.weixin.qq.com/cgi-bin/groups/members/batchupdate',
        'set_industry'  => 'https://api.weixin.qq.com/cgi-bin/template/api_set_industry', // 设置所属行业
        'get_industry'  => 'https://api.weixin.qq.com/cgi-bin/template/get_industry', // 获取设置的行业信息
        'add_template'  => 'https://api.weixin.qq.com/cgi-bin/template/api_add_template', // 获得模板ID
        'get_template'  => 'https://api.weixin.qq.com/cgi-bin/template/get_all_private_template', // 获取模板列表
        'del_template'  => 'https://api.weixin.qq.com/cgi-bin/template/del_private_template', // 删除模板
        'send_template' => 'https://api.weixin.qq.com/cgi-bin/message/template/send', // 发送模板消息
        'access_token' => 'https://api.weixin.qq.com/cgi-bin/token', // 获取ACCESS_TOKEN
        'qrcode_create' => 'https://api.weixin.qq.com/cgi-bin/qrcode/create',
        'qrcode_show'   => 'https://mp.weixin.qq.com/cgi-bin/showqrcode',
        'short_url'     => 'https://api.mch.weixin.qq.com/tools/shorturl', // 转换短链接
    );
    /**
     * 返回数据结果集
     * @var mixed
     */
    public static $result;

    public static function api($url, $params = array(), $method = 'GET')
    {
        $result = self::http($url, $params, $method);
//        return $result;
        return self::parseRequestJson($result);
    }

    /**
     * 发送HTTP请求方法，目前只支持CURL发送请求
     * @param  string  $url    请求URL
     * @param  array   $params 请求参数
     * @param  string  $method 请求方法GET/POST
     * @param  boolean $ssl    是否进行SSL双向认证
     * @return array   $data   响应数据
     */
    public static function http($url, $params = array(), $method = 'GET', $ssl = false)
    {
        $opts = array(
            CURLOPT_TIMEOUT        => 30,
            CURLOPT_RETURNTRANSFER => 1,
            CURLOPT_SSL_VERIFYPEER => false,
            CURLOPT_SSL_VERIFYHOST => false,
        );
        /* 根据请求类型设置特定参数 */
        switch (strtoupper($method)) {
            case 'GET':
                $getQuerys         = !empty($params) ? '?' . urldecode(http_build_query($params)) : '';
                $opts[CURLOPT_URL] = $url . $getQuerys;
                break;
            case 'POST':
                $opts[CURLOPT_URL]        = $url;
                $opts[CURLOPT_POST]       = 1;
                $opts[CURLOPT_POSTFIELDS] = $params;
                break;
        }
        /* 初始化并执行curl请求 */
        $ch = curl_init();
        curl_setopt_array($ch, $opts);
        $data   = curl_exec($ch);
        $err    = curl_errno($ch);
        $errmsg = curl_error($ch);
        curl_close($ch);
        if ($err > 0) {
            dump('CURL:' . $errmsg);
            //exit;
            self::errors('CURL:' . $errmsg);
            return false;
        } else {
            return $data;
        }
    }

    /**
     * XML文档解析成数组，并将键值转成小写
     * @param  xml   $xml
     * @return array
     */
    public static function xml2array($xml)
    {
        $data = (array) simplexml_load_string($xml, 'SimpleXMLElement', LIBXML_NOCDATA);
        return array_change_key_case($data, CASE_LOWER);
    }

    /**
     * 将数组转换成XML
     * @param  array $array
     * @return xml
     */
    public static function array2xml($array = array())
    {
        $xml = new \SimpleXMLElement('<xml></xml>');
        self::_data2xml($xml, $array);
        return $xml->asXML();
    }

    /**
     * 数据XML编码
     * @param  xml    $xml  XML对象
     * @param  mixed  $data 数据
     * @param  string $item 数字索引时的节点名称
     * @return string xml
     */
    private static function _data2xml($xml, $data, $item = 'item')
    {
        foreach ($data as $key => $value) {
            is_numeric($key) && $key = $item;
            if (is_array($value) || is_object($value)) {
                $child = $xml->addChild($key);
                self::_data2xml($child, $value, $item);
            } else {
                if (is_numeric($value)) {
                    $child = $xml->addChild($key, $value);
                } else {
                    $child = $xml->addChild($key);
                    $node  = dom_import_simplexml($child);
                    $node->appendChild($node->ownerDocument->createCDATASection($value));
                }
            }
        }
    }

    /**
     * 解析返回的json数据
     * @param  [type] $json
     * @return
     */
    private static function parseRequestJson($json)
    {
        $result = json_decode($json, true);
        if (isset($result['errcode'])) {
            if ($result['errcode'] == 0) {
                self::$result = $result;
                return true;
            } else {
                self::errors(self::parseError($result));
                return self::parseError($result);
            }
        } else {
            return $result;
        }
    }

    /**
     * 解析错误信息
     * @param  array  $result
     * @return string
     */
    public static function parseError($result)
    {
        $code = $result['errcode'];
        return '[' . $code . '] ' . self::$errMsg[$code];
    }
    /**
     * 获取用户信息(不用授权)可以判断用户是否关注微信号
     * @param  [type] $openid [description]
     * @param  [type] $lang   [description]
     * @return [type]         [description]
     */
    public static function userinfo($openid,$token, $lang = 'zh_CN')
    {
        $params = array(
            'openid'       => $openid,
            'access_token' => $token,
            'lang'         => $lang,
        );
        $result = self::api(self::$urlf['user_info'], $params);
        if ($result) {
            return $result;
        } else {
            return false;
        }
    }

    /**
     * 自动获取用户的openid
     * 2016.5.20
     */
    public static function authorize_openid()
    {
        if (empty($_GET['code'])) {
            //只获取用户的openid
            $params = array(
                'appid'         => 'wx663e72eb678b9a9e',
                'redirect_uri'  => preg_replace('#&code=(\\w+)#', '', 'http://www.jiurong.com' . $_SERVER['REQUEST_URI']),
                'response_type' => 'code',
                'scope'         => 'snsapi_base',
                'state'         => 'STATE',)
            ;
            $oauthUrl =  'https://open.weixin.qq.com/connect/oauth2/authorize?' . http_build_query($params) . '#wechat_redirect';
            redirect($oauthUrl);
            exit();
        }else {
            if (isset($_GET['code']) && isset($_GET['state'])) {
                //通过code获取Access Token
                $code = isset($_GET['code']) ? $_GET['code'] : '';
                if (!$code) {
                    $arr['info'] = "未获取到CODE信息";
                    $arr['error'] = 1;
                    return $arr;
                }
                $params = array(
                    'appid'      => 'wx663e72eb678b9a9e',
                    'secret'     => 'c85a7728df7b729688dcb017a69de815',
                    'code'       => $code,
                    'grant_type' => 'authorization_code',
                );
                $return = self::api('https://api.weixin.qq.com/sns/oauth2/access_token', $params);
                if($return['errcode']){
                    $arr['info'] = "授权失败";
                    $arr['error'] = 1;
                    return $arr;
                }
                return $return;
            }
        }
    }
    
    /**
     * 获取全部关注用户
     * @param  [type] $nextOpenid 第一个拉取的OPENID，不填默认从头开始拉取
     * 2016.5.20  获取openid
     */
    public static function getAllUser($nextOpenid = '')
    {
        $redis = new Redis();
        $redis->connect(C("commision_redis_host"),C("commision_redis_port"));
        if($redis->exists($token)){
            $token = unserialize($redis->get($token));
            $token = $token;
        }else{
            $token = Utils::getToken();
        }
        $redis->close();
        $params = array(
            'next_openid'  => $nextOpenid,
            'access_token' => $token,
        );
        $result = self::api(self::$urlf['user_get'], $params);
        if ($result) {
            return $result['data']['openid'];
        } else {
            return self::parseError($result);
        }
    }

    /**
     * 获取客户端IP地址
     * @param integer $type 返回类型 0 返回IP地址 1 返回IPV4地址数字
     * @param boolean $adv 是否进行高级模式获取（有可能被伪装）
     * @return mixed
     */
    private static function _getClientIp($type = 0, $adv = true)
    {
        $type      = $type ? 1 : 0;
        static $ip = null;
        if ($ip !== null) {
            return $ip[$type];
        }

        if ($adv) {
            if (isset($_SERVER['HTTP_X_FORWARDED_FOR'])) {
                $arr = explode(',', $_SERVER['HTTP_X_FORWARDED_FOR']);
                $pos = array_search('unknown', $arr);
                if (false !== $pos) {
                    unset($arr[$pos]);
                }

                $ip = trim($arr[0]);
            } elseif (isset($_SERVER['HTTP_CLIENT_IP'])) {
                $ip = $_SERVER['HTTP_CLIENT_IP'];
            } elseif (isset($_SERVER['REMOTE_ADDR'])) {
                $ip = $_SERVER['REMOTE_ADDR'];
            }
        } elseif (isset($_SERVER['REMOTE_ADDR'])) {
            $ip = $_SERVER['REMOTE_ADDR'];
        }
        // IP地址合法验证
        $long = sprintf("%u", ip2long($ip));
        $ip   = $long ? array($ip, $long): array('0.0.0.0', 0);
        return $ip[$type];
    }
}

/**
 * 消息推送
 *  模板消息
 */
class Template extends Utils
{
    /**
     * 设置所属行业
     * @param [type] $industry1 [description]公众号模板消息所属行业编号
     * @param [type] $industry2 [description]公众号模板消息所属行业编号
     */
    public static function setIndustry($primary, $secondary)
    {
        $params = array(
            'industry_id1' => $primary,
            'industry_id2' => $secondary,
        );
        $params = json_encode($params);
        $redis = new Redis();
        $redis->connect(C("commision_redis_host"),C("commision_redis_port"));
        if($redis->exists($token)){
            $token = unserialize($redis->get($token));
            $info = $token;
        }else{
            $info = parent::getToken();
        }
        $redis->close();
        if(!$info){
            return $info;
        }else{
            return parent::api(parent::$urlf['set_industry'] . '?access_token=' . $info, $params, 'POST');
        }
    }

    /**
     * 获取设置的行业信息
     * @return [type] [description]
     * primary_industry	帐号设置的主营行业
    secondary_industry	帐号设置的副营行业
     */
    public static function getIndustry()
    {
        $redis = new Redis();
        $redis->connect(C("commision_redis_host"),C("commision_redis_port"));
        if($redis->exists($token)){
            $token = unserialize($redis->get($token));
            $info = $token;
        }else{
            $info = parent::getToken();
        }
        $redis->close();
        if(!$info){
            return $info;
        }else{
            return parent::api(parent::$urlf['get_industry'] . '?access_token=' . $info, '', 'GET');
        }
    }

    /**
     * 获得模板ID
     * @return [type] [description]模板库中模板的编号，有“TM**”和“OPENTMTM**”等形式
     */
    public static function add($shortTemplateId)
    {
        $params = array(
            'template_id_short' => $shortTemplateId,
        );
        $params = json_encode($params);
        $redis = new Redis();
        $redis->connect(C("commision_redis_host"),C("commision_redis_port"));
        if($redis->exists($token)){
            $token = unserialize($redis->get($token));
            $info = $token;
        }else{
            $info = parent::getToken();
        }
        $redis->close();
        if(!$info){
            //编号有误，，您还没申请该模板
            return $info;
        }else{
            return parent::api(parent::$urlf['add_template'] . '?access_token=' .$info, $params, 'POST');
        }
    }

    /**
     * 获取模板列表
     * @return [type] [description]
     */
    public static function getTemplateList()
    {
        $redis = new Redis();
        $redis->connect(C("commision_redis_host"),C("commision_redis_port"));
        if($redis->exists($token)){
            $token = unserialize($redis->get($token));
            $info = $token;
        }else{
            $info = parent::getToken();
        }
        $redis->close();
        if(!$info){
            return $info;
        }else{
            return parent::api(parent::$urlf['get_template'] . '?access_token=' . $info, '', 'GET');
        }
    }

    /**
     * 删除模板
     * @param  [type] $templateId 长模板ID 例：Dyvp3-Ff0cnail_CDSzk1fIc6-9lOkxsQE7exTJbwUE
     * @return boolean
     */
    public static function delete($templateId)
    {
        $params = array(
            'template_id' => $templateId,
        );
        $params = json_encode($params);
        $redis = new Redis();
        $redis->connect(C("commision_redis_host"),C("commision_redis_port"));
        if($redis->exists($token)){
            $token = unserialize($redis->get($token));
            $info = $token;
        }else{
            $info = parent::getToken();
        }
        $redis->close();
        if(!$info){
            return $info;
        }else{
            return parent::api(parent::$urlf['del_template'] . '?access_token=' . $info, $params, 'POST');
        }
    }

    /**
     * 发送模板消息
     * @param  [type] $openid     接收用户
     * @param  [type] $templateId 模板ID
     * @param  array  $data       消息体
     * @param  string $url        连接URL
     * @return boolean
     */
    public static function send($openid, $templateId, $data = array(), $url = '')
    {
        $params = array(
            'touser'      => $openid,
            'template_id' => $templateId,
            'url'         => $url,
            'data'        => $data,
        );
        $params = json_encode($params, JSON_UNESCAPED_UNICODE);
        $redis = new Redis();
        $redis->connect(C("commision_redis_host"),C("commision_redis_port"));
        if($redis->exists($token)){
            $token = unserialize($redis->get($token));
            $info = $token;
        }else{
            $info = parent::getToken();
        }
        $redis->close();
        if(!$info){
            return $info;
        }else{
            return parent::api(parent::$urlf['send_template'] . '?access_token=' . $info, $params, 'POST');
        }
    }
}

/**
 * 生成二维码
 * 推广支持
 */
class QRcode extends Utils
{

    /**
     * 接口名称与URL映射
     * @var array
     */

    /**
     * 临时二维码
     * @param  [type]  $scene_id [description]场景值ID，
     * @param  integer $expire   [description]过期时间
     * @return [type]            [description]
     */
    public static function tempQrcode($scene_id, $expire = 604800)
    {
        $params = array(
            'expire_seconds' => $expire,
            'action_name'    => 'QR_SCENE',
            'action_info'    => array(            //二维码详细信息
                'scene' => array(
                    'scene_id' => $scene_id,      //场景值ID，
                ),
            ),
        );
        $params = json_encode($params, JSON_UNESCAPED_UNICODE);
        $redis = new Redis();
        $redis->connect(C("commision_redis_host"),C("commision_redis_port"));
        if($redis->exists($token)){
            $token = unserialize($redis->get($token));
            $info = $token;
        }else{
            $info = parent::getToken();
        }
        $redis->close();
        $result = Utils::api(parent::$urlf['qrcode_create'] . '?access_token=' . $info, $params, 'POST');
        if ($result) {
            //通过连接返回图片
            return parent::$urlf['qrcode_show'] . '?ticket=' . $result['ticket'];
        } else {
            return false;
        }
    }

    /**
     * 永久二维码
     * @return [type] [description]
     */
    public static function limitQrcode($scene_str)
    {
        $redis = new Redis();
        $redis->connect(C("commision_redis_host"),C("commision_redis_port"));
        if($redis->exists($token)){
            $token = unserialize($redis->get($token));
            $info = $token;
        }else{
            $info = parent::getToken();
        }
        $redis->close();
        $params = array(
            'action_name'  => 'QR_LIMIT_SCENE',
            'action_info'  => array(
                'scene' => array(
                    'scene_str' => $scene_str,
                ),
            ),
            'access_token' => $info,
        );
        $params = json_encode($params, JSON_UNESCAPED_UNICODE);
        $result = parent::api(parent::$urlf['qrcode_create'] . '?access_token=' . $info, $params, 'POST');
        if ($result) {
            return parent::$urlf['qrcode_show'] . '?ticket=' . $result['ticket'];
        } else {
            return false;
        }
    }

    /**
     * 转换短链接
     * @param  [type] $longUrl
     * @return [type]
     */
    public static function short($longUrl)
    {
        $redis = new Redis();
        $redis->connect(C("commision_redis_host"),C("commision_redis_port"));
        if($redis->exists($token)){
            $token = unserialize($redis->get($token));
            $info = $token;
        }else{
            $info = parent::getToken();
        }
        $redis->close();
        $params = array(
            'action'   => 'long2short',
            'long_url' => $longUrl,
        );
        $params = json_encode($params);
        $result = parent::api(parent::$urlf['short_url'] . '?access_token=' . $info, $params, 'POST');
        if ($result) {
            return $result;
        } else {
            return false;
        }
    }
}


/**
 * 自动回复
 */
class Reply extends Utils
{

    /**
     * 接收到的消息内容
     * @var array
     */
    private static $request = array();

    private static $response = array();

    /**
     * 接受消息,通用,接受到的消息
     * 用户自己处理消息类型就可以
     * 暂时不处理加密问题
     * @return array|boolean
     */
    public static function request()
    {
        // 它说是POST,然并卵..
        $postStr = isset($GLOBALS["HTTP_RAW_POST_DATA"]) ? $GLOBALS["HTTP_RAW_POST_DATA"] : null;

        if (!empty($postStr)) {
            $data = parent::xml2array($postStr);
            return self::$request = $data;
        } else {
            return false;
        }
    }

    /**
     * 回复消息
     * @param  [type] $content [description]
     * @param  string $type    [description]
     * @return [type]          [description]
     */
    public static function replys($content, $type = 'text')
    {
        /* 基础数据 */
        self::$response = array(
            'ToUserName'   => self::$request['fromusername'],
            'FromUserName' => self::$request['tousername'],
            'CreateTime'   => time(),
            'MsgType'      => $type,
        );
        /* 添加类型数据 */
        self::$type($content);
        /* 转换数据为XML */
            $response = parent::array2xml(self::$response);
        //发送
        exit($response);
    }

    /**
     * 回复文本类型消息
     * @param  string $content
     */
    private static function text($content)
    {
        self::$response['Content'] = $content;
    }
}
