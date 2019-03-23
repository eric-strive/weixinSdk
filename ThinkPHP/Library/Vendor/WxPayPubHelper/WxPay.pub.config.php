<?php
/**
* 	配置账号信息
*/

class WxPayConf_pub
{
	//=======【基本信息设置】=====================================
	//微信公众号身份的唯一标识。审核通过后，在微信发送的邮件中查看
	const APPID = 'wxf11111111111111';
	//受理商ID，身份标识
	const MCHID = '15083111111';
	//商户支付密钥Key。审核通过后，在微信发送的邮件中查看
	const KEY = '6410439f049098711111111111111111';
//	const KEY = '23e016bd8c4b52c096fb18f065e8a289';

	//JSAPI接口中获取openid，审核后在公众平台开启开发模式后可查看
	const APPSECRET = '0dd553e987ba5675111111111111111111';
	
	//=======【JSAPI路径设置】===================================
	//获取access_token过程中的跳转uri，通过跳转将code传入jsapi支付页面
	const JS_API_CALL_URL = 'http://XXXxxxxxx.com/jjw/Donation/Index/donation';
	
	//=======【证书路径设置】=====================================
	//证书路径,注意应该填写绝对路径
	const SSLCERT_PATH = 'http://xxxxxxx.com/jjw/ThinkPHP/Library/Vendor/WxPayPubHelper/cacert/apiclient_cert.pem';
	const SSLKEY_PATH = 'http://xxxxxxxx.com/jjw/ThinkPHP/Library/Vendor/WxPayPubHelper/cacert/apiclient_key.pem';
	const SSLROOTCA_PATH = 'http://xxxxxxx.com/jjw/ThinkPHP/Library/Vendor/WxPayPubHelper/cacert/rootca.pem';


	//=======【异步通知url设置】===================================
	//异步通知url，商户根据实际开发过程设定
	const NOTIFY_URL = 'http://xxxxxxxxx.com/jjw/Donation/Notify/notify';

	//=======【curl超时设置】===================================
	//本例程通过curl使用HTTP POST方法，此处可修改其超时时间，默认为30秒
	const CURL_TIMEOUT = 30;
}
	
?>