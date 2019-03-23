<?php
namespace Weixin\Controller;
use OT\DataDictionary;
header("access-control-allow-origin:*");
class EventController extends CommonController
{
    public $token = 'xxxxxx';//与微信公众号后台设置保持一致
    /**
     * @return bool
     * @throws Exception
     * 验证签名, 手册中原代码改写
     * 这里使用户确认这个链接是会访问
     */
    public function index(){
        if (!isset($_GET['echostr'])) {
            //调用wecat对象中的方法响应用户消息
            $this->responseMsg();
        }else{
            //调用valid()方法，进行token验证

            $echoStr = $_GET["echostr"];
            //先定义token
            if(!$this->token){
                throw new Exception('TOKEN is not defined!');
            }
            $signature = $_GET["signature"];
            $timestamp = $_GET["timestamp"];
            $nonce = $_GET["nonce"];
            $token = $this->token;
            $tmpArr = array($token, $timestamp, $nonce);
            sort($tmpArr, SORT_STRING);
            $tmpStr = implode( $tmpArr );
            $tmpStr = sha1( $tmpStr );
            if( $tmpStr == $signature ){
                echo $echoStr;
                exit;
            }else{
                return false;
            }
        }
    }
    /**
     * 响应消息处理
     * 对于任何事件处理都经过这个函数处理
     */
    public function responseMsg()
    {
        //接收微新传过来的xml消息数据
        $postStr = $GLOBALS["HTTP_RAW_POST_DATA"];
        //如果接收到了就处理并回复
        if (!empty($postStr)){
            //将接收的消息处理返回一个对象，将xml数据转换为php数组
            $postObj = simplexml_load_string($postStr, 'SimpleXMLElement', LIBXML_NOCDATA);
            //从消息对象中获取消息的类型 event text  image location voice vodeo link，提取响应的类型
            $RX_TYPE = trim($postObj->MsgType);
            //消息类型分离, 通过RX_TYPE类型作为判断， 每个方法都需要将对象$postObj传入
            switch ($RX_TYPE)
            {
                case "event":
                    $result = $this->receiveEvent($postObj);    //事件处理
                    break;
                case "text":
                    $result = $this->receiveText($postObj);    //事件处理
                    break;
                default:
                    $result = $this->receiveEvent($postObj);    //事件处理
            }
            echo $result;
        }else {
            echo "";
            exit;
        }
    }
    /**
     * 接收事件消息
     * @param $object：公众号传过来的数据
     * @return mixed
     */
    private function receiveEvent($object)
    {
        //临时定义一个变量， 不同的事件发生时， 给用户反馈不同的内容
        $content = "";
        //通过用户发过来的不同事件做处理
        switch ($object->Event)
        {
            //用户一关注 触发的事件
            case "subscribe":
                $content = "2018年7月20日-2018年8月20日，闯过7关，立领500元。\n点击“我要闯关”，开始闯关。\n点击“闯关说明”，查看“闯关说明”了解闯关规则。";
                $user = M("user");
                $openid = $object->FromUserName;
                $EventKey = "$object->EventKey";
                $userinfo = $this->getUserInfo($openid);
                if(!empty($EventKey)){
                    $uid = str_replace("qrscene_","",$EventKey);
                    $uid = intval($uid);
                    $ufinfo = $user->where("uid=".$uid)->find();
                    if($ufinfo){
                        $user_f_info = M("jjw_userinfo")->where("uid=".$uid)->find();
                        if($user_f_info){
                            $userinfo['referrer_user'] = $user_f_info['realname'];
                            $userinfo['referrer_store'] = $user_f_info['store_id'];
                            $content.=',/n由'.$user_f_info['realname'].'推荐';
                        }
                        $userinfo['stair_id'] = $uid;
                        $userinfo['second_id'] = $ufinfo['stair_id'];
                        $userinfo['three_id'] = $ufinfo['second_id'];
                    }
                }
                $uinfo = $user->where('openid="'.$openid.'"')->find();
                $userinfo['addtime'] = time();
                if($uinfo){
                    break;
                }else{
                    $we = $user->add($userinfo);
                    if($we){
                        $file = $this->ticket($we);
                        $user->where('openid= "'.$openid.'"')->setField('img_url',$file["url"]);
                        $user->where('openid= "'.$openid.'"')->setField('img',$file["filename"]);
                    }
                }
                break;
            //取消关注时触发的事件
            case "unsubscribe":
                $content = "取消关注";
                //用户在取消关注时将表中的关注列设置为0
                $openid = $object->FromUserName;
                $weiinfo = M('recommend', 'oa_wx_', 'OA_DB');
                $data['status'] = 0;
                $weiinfo->where('openid= "'.$openid.'"')->setField('status',0);
                break;
            case "MASSSENDJOBFINISH":
                $content = "您好，请问您有什么需要帮助的。\n回复“1”查看什么是硼呗；\n回复“2”查看“查看分红技巧”；\n回复“3”查看消费代言技巧";
                break;
            default:
                $content = "2018年7月20日-2018年8月20日，闯过7关，立领500元。\n点击“我要闯关”，开始闯关。\n点击“闯关说明”，查看“闯关说明”了解闯关规则。";
        }
        if(is_array($content)){
            if (isset($content[0])){
                $result = $this->transmitNews($object, $content);
            }else if (isset($content['MusicUrl'])){
                $result = $this->transmitMusic($object, $content);
            }
        }else{
            $result = $this->transmitText($object, $content);
        }

        return $result;
    }
    private function receiveText($object)
    {
        //临时定义一个变量， 不同的事件发生时， 给用户反馈不同的内容
        $content = "";
        //通过用户发过来的不同事件做处理
        switch ($object->Content)
        {
            //用户一关注 触发的事件
            case 1:
//                $result = $this->receiveImage($object,'adcUSPLT8Qor9AhB4WpJxjx33SzazjLdtojpB0bzS3FkCtSkloB5dxKW7GYI530o');
                $content = array();
                $content[] = array("Title"=>"关于硼呗",
                    "PicUrl"=>"https://mmbiz.qpic.cn/mmbiz_png/nTSc2xgjfgaMeRfB1iaxuzRhd8qXbHaWziau1fU0AeVR4zJTibDzq8Jr8XYHvm7icmhficvXkVQYPibicVQo2vHOWvZ9w/0?wx_fmt=png",
                    "Url" =>"http://myjjfww.com/jjw/Home/Usercenter/explain");
                $result = $this->transmitNews($object, $content);
                break;
            case 2:
//                $result = $this->receiveImage($object,'cI3KiM_YioPsE7Qmj8A1P0oVBn9ZxO4BZu15plEd6DYbMT2DyThj_UW64Xj-2eoT');
                $content = array();
                $content[] = array("Title"=>"分红技巧",
                    "PicUrl"=>"https://mmbiz.qpic.cn/mmbiz_png/nTSc2xgjfgaMeRfB1iaxuzRhd8qXbHaWzqsL0QHRr5jAibibRZjJGJIIwgSWMnTSQQib1Y3rBpkGiaHAXMZW5oDbapw/0?wx_fmt=png",
                    "Url" =>"http://myjjfww.com/jjw/Home/Usercenter/participation_skill");
                $result = $this->transmitNews($object, $content);
                break;
            case 3:
                //回复单图文消息
                $content = array();
				 $content[] = array("Title"=>"代言技巧",
                    "PicUrl"=>"https://mmbiz.qpic.cn/mmbiz_png/nTSc2xgjfgaMeRfB1iaxuzRhd8qXbHaWzzr044k2HDlEdg6ziaiaMl8IjBGS7NwIDopgMYUKK4tezhBlmwiawM5YGA/0?wx_fmt=png",
                    "Url" =>"http://myjjfww.com/jjw/Home/Usercenter/represent_explain");
  //              $content[] = array("Title"=>"单身终结计划",
  //                                 "Description"=>"单身终结计划：
//1、第一步：扫描二维码，进入三观匹配页面，设置匹配问题；
//2、第二步：男生选择“我买单”，线下支付商品金额，获得消费券；
//3、第三步：在用户中心，点击“我的活动”，查看“匹配情况”；
//4、第四步：双方同时同意和对方交往以后，可以查看对方微信帐号；
//5、第五步：添加对方微信，初步交流，并越好时间共同前往店铺消费套餐；
//6、第六步：向商家提供消费券帐号，即可和你的另一半享受情侣套餐。",
//                                   "PicUrl"=>"https://mmbiz.qpic.cn/mmbiz_jpg/LkffyNZLwtJrTliaPTeYX5ndFFJP0nornrIibqMqAsMAOicklYNhRtTcqyRYyic2d6JdPicckAdy52seZDy3lJcHSkQ/0?wx_fmt=jpeg",
 //                                  "Url" =>"http://myjjfww.com/jjw/Home/Usercenter/represent_explain");
                $result = $this->transmitNews($object, $content);
//                $result = $this->receiveImage($object,'Mxqb5YLGJRavETsxFv0T9xIDoQBcf4kMsZa_GLqtx09-_aMIZg-5pn7M7NLX106P');
                break;
            default:
                $content = "2018年7月20日-2018年8月20日，闯过7关，立领500元。\n点击“我要闯关”，开始闯关。\n点击“活动中心”，查看“闯关说明”了解闯关规则。";
//                $content = "您好，请问您有什么需要帮助的。\n回复“1”查看如何参加“消费分红”活动；\n回复“2”查看“分红技巧”；\n回复“3”查看“消费代言技巧。";
                if(is_array($content)){
                    if (isset($content[0])){
                        $result = $this->transmitNews($object, $content);
                    }else if (isset($content['MusicUrl'])){
                        $result = $this->transmitMusic($object, $content);
                    }
                }else{
                    $result = $this->transmitText($object, $content);
                }
                break;
        }
        return $result;
    }
    //回复文本消息
    private function transmitText($object, $content)
    {
        $xmlTpl = "<xml>
<ToUserName><![CDATA[%s]]></ToUserName>
<FromUserName><![CDATA[%s]]></FromUserName>
<CreateTime>%s</CreateTime>
<MsgType><![CDATA[text]]></MsgType>
<Content><![CDATA[%s]]></Content>
</xml>";
        $result = sprintf($xmlTpl, $object->FromUserName, $object->ToUserName, time(), $content);
        return $result;
    }
    //回复图文消息
    private function transmitNews($object, $newsArray)
    {
        if(!is_array($newsArray)){
            return;
        }
        $itemTpl = "    <item>
        <Title><![CDATA[%s]]></Title>
        <Description><![CDATA[%s]]></Description>
        <PicUrl><![CDATA[%s]]></PicUrl>
        <Url><![CDATA[%s]]></Url>
    </item>
";
        $item_str = "";
        foreach ($newsArray as $item){
            $item_str .= sprintf($itemTpl, $item['Title'], $item['Description'], $item['PicUrl'], $item['Url']);
        }
        $xmlTpl = "<xml>
<ToUserName><![CDATA[%s]]></ToUserName>
<FromUserName><![CDATA[%s]]></FromUserName>
<CreateTime>%s</CreateTime>
<MsgType><![CDATA[news]]></MsgType>
<ArticleCount>%s</ArticleCount>
<Articles>
$item_str</Articles>
</xml>";

        $result = sprintf($xmlTpl, $object->FromUserName, $object->ToUserName, time(), count($newsArray));
        return $result;
    }
    private function receiveImage($object,$MediaId)
    {
        //回复图片消息
        $content = array("MediaId"=>$MediaId);
        $result = $this->transmitImage($object, $content);;
        return $result;
    }
    /*
     * 回复图片消息
     */
    private function transmitImage($object, $imageArray)
    {
        $itemTpl = "<Image>
    <MediaId><![CDATA[%s]]></MediaId>
</Image>";

        $item_str = sprintf($itemTpl,$imageArray['MediaId']);

        $textTpl = "<xml>
<ToUserName><![CDATA[%s]]></ToUserName>
<FromUserName><![CDATA[%s]]></FromUserName>
<CreateTime>%s</CreateTime>
<MsgType><![CDATA[image]]></MsgType>". $item_str. "</xml>";

        $result = sprintf($textTpl, $object->FromUserName, $object->ToUserName, time());
        return $result;
    }
}
