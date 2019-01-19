<?php
namespace Vendor\Wechat;
class JsSdk {
  private $appId;
  private $appSecret;
  private $accessToken;
  private $ticket;

  public function __construct($appId, $appSecret,$accessToken=null,$ticket=null) {
    	$this->appId = $appId;
    	$this->appSecret = $appSecret;
    	$this->accessToken = $accessToken;
    	$this->ticket = $ticket;
  }

  public function getSignPackage() {
    	$jsapiTicket = $this->ticket;

    	// 注意 URL 一定要动态获取，不能 hardcode.
    	$protocol = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off' || $_SERVER['SERVER_PORT'] == 443) ? "https://" : "http://";
    	$url = "$protocol$_SERVER[HTTP_HOST]$_SERVER[REQUEST_URI]";

    	$timestamp = time();
    	$nonceStr = $this->createNonceStr();

    	// 这里参数的顺序要按照 key 值 ASCII 码升序排序
    	$string = "jsapi_ticket=$jsapiTicket&noncestr=$nonceStr&timestamp=$timestamp&url=$url";

   	$signature = sha1($string);

    	$signPackage = array(
      		"appId"     => $this->appId,
      		"nonceStr"  => $nonceStr,
      		"timestamp" => $timestamp,
      		"url"       => $url,
      		"signature" => $signature,
      		"rawString" => $string
   	 );
    	return $signPackage; 
  }

  private function createNonceStr($length = 16) {
    	$chars = "abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789";
    	$str = "";
    	for ($i = 0; $i < $length; $i++) {
      		$str .= substr($chars, mt_rand(0, strlen($chars) - 1), 1);
    	}
    	return $str;
  }

  public function getJsApiTicket() {
      	$accessToken = $this->accessToken;
      	// 如果是企业号用以下 URL 获取 ticket
      	// $url = "https://qyapi.weixin.qq.com/cgi-bin/get_jsapi_ticket?access_token=$accessToken";
      	$url = "https://api.weixin.qq.com/cgi-bin/ticket/getticket?type=jsapi&access_token=$accessToken";
      	$res = json_decode($this->httpGet($url),true);
     	 if (is_array($res)) {
     	 	if($res['errcode']){
     	 		throw new \Exception($res['errmsg']);
     	 	}else{
     	 		$this->ticket = $res['ticket'];
        			return $res;
     	 	}
      	}else{
      		throw new \Exception('获取微信access_token失败！');
      	}	 
  }

  public function getAccessToken() {
      	// 如果是企业号用以下URL获取access_token
     	// $url = "https://qyapi.weixin.qq.com/cgi-bin/gettoken?corpid=$this->appId&corpsecret=$this->appSecret";
     	$url = "https://api.weixin.qq.com/cgi-bin/token?grant_type=client_credential&appid=$this->appId&secret=$this->appSecret";
      	$res = json_decode($this->httpGet($url),true);
     	if (is_array($res)) {
      		if($res['errcode']){
     	 		throw new \Exception($res['errmsg']);
     	 	}else{
     	 		$this->accessToken = $res['access_token'];
        			return $res;file:///var/www/huifu/data.json

     	 	}
      	}else{
      		throw new \Exception('获取微信access_token失败！');
      	}
  }

  private function httpGet($url) {
    	$curl = curl_init();
    	curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
    	curl_setopt($curl, CURLOPT_TIMEOUT, 500);
    	curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);
    	curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, false);
    	curl_setopt($curl, CURLOPT_URL, $url);

    	$res = curl_exec($curl);
    	curl_close($curl);

   	 return $res;
  }
}

