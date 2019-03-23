# weixinSdk
里面主要包含微信支付、微信模版消息发送、响应用户消息、OAuth2.0授权登录
、微信JS-SDK相关接口、支付支付等；
首先对接微信，在文件/ThinkPHP/Library/Vendor/WxPayPubHelper/WxPay.pub.config.php里面填写微信配置的相关信息
### 这里的信息微信支付用的；
const APPID = 'wxf11111111111111';//受理商ID，身份标识
const MCHID = '15083111111';//商户支付密钥Key。审核通过后，在微信发送的邮件中查看
const KEY = '6410439f049098711111111111111111';
#### JSAPI接口中获取openid，审核后在公众平台开启开发模式后可查看
const APPSECRET = '0dd553e987ba5675111111111111111111';
#### 获取access_token过程中的跳转uri，通过跳转将code传入jsapi支付页面
const JS_API_CALL_URL = 'http://XXXxxxxxx.com/Donation/Index/donation';
#### 证书路径,注意应该填写绝对路径,正式需要在公众号后台下载
const SSLCERT_PATH = 'http://xxxxxxx.com/ThinkPHP/Library/Vendor/WxPayPubHelper/cacert/apiclient_cert.pem';
const SSLKEY_PATH = 'http://xxxxxxxx.com/ThinkPHP/Library/Vendor/WxPayPubHelper/cacert/apiclient_key.pem';
const SSLROOTCA_PATH = 'http://xxxxxxx.com/ThinkPHP/Library/Vendor/WxPayPubHelper/cacert/rootca.pem';
#### 异步通知url，商户根据实际开发过程设定
const NOTIFY_URL = 'http://xxxxxxxxx.com/Donation/Notify/notify';
#### 路径Application/Weixin/Controller/EventController.class.php
这个是用户与公众号有交互时用到的，这里$token的值需要设置和你公众号后台的一致；
### 支付宝支付相关配置
#### 路径 /Application/Weixin/Controller/AliController.class.php

#### 项目主要代码地址/Application/Weixin