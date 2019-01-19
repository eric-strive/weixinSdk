<?php
namespace Home\ORG\Util;
class Jmr{
    //CURL请求的函数http_request()
    public function http($url, $params = array(), $method = 'get', $ssl = false)
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
     * 新增会员信息
     */
    public function add_vip($openid,$user_mobile){
        $url = 'http://114.215.144.156:2233/api/add_vipinfo';
        $params = array(
            'user_mobile'=>$user_mobile,
            'openid'=>$openid,
        );
        $return =  $this->http($url,$params);
        return json_decode($return,true);
    }
    /**
     * 查询会员信息
     */
    public function query_vip($openid){
        $url = 'http://114.215.144.156:2233/api/query_vipinfo';
        $params = array(
            'openid'=>$openid,
        );
        $return =  $this->http($url,$params);
        return json_decode($return,true);
    }
    /**
     * 3.	修改会员信息
     */
    public function update_vip($params){
        $url = 'http://114.215.144.156:2233/api/update_vipinfo';
        $return =  $this->http($url,$params);
        return json_decode($return,true);
    }
    /**
     * 所有会员信息
     */
    public function query_vips(){
        $url = 'http://114.215.144.156:2233/api/query_vipinfos';
        $return = $this->http($url);
        return json_decode($return,true);
    }
    /**
     * 充值
     */
    public function viprecharge($user_id,$openid,$real_money,$referee_mobile,$out_trade_no){
        $url = 'http://114.215.144.156:2233/api/viprecharge';
        $params = array(
            'openid'=>$openid,
            'real_money'=>$real_money,
            'user_id'=>$user_id,
            'referee_mobile'=>$referee_mobile,
            'out_trade_no'=>$out_trade_no,
            'recharge_datetime'=>date('Y-m-d H:i:s')
        );
        $return =  $this->post2($url,$params);
        return json_decode($return,true);
    }
    /**
     * 6.	查询会员充值记录
     */
    public function query_recharge($openid){
        $url = 'http://114.215.144.156:2233/api/query_recharge';
        $params = array(
            'openid'=>$openid,
        );
        $return =  $this->http($url,$params);
        return json_decode($return,true);
    }
    /**
     * 会员消费
     */
    public function vipsale($openid,$sale_money,$out_trade_no){
        $url = 'http://114.215.144.156:2233/api/vipsale';
        $params = array(
            'openid'=>$openid,
            'sale_money'=>$sale_money,
            'order_id'=>$out_trade_no,
            'sale_datetime'=>date('Y-m-d H:i:s'),
        );
        $return =  $this->http($url,$params);
        return json_decode($return,true);
    }
    /**
     * 会员消费记录
     */
    public function query_vipsale($openid){
        $url = 'http://114.215.144.156:2233/api/query_vipsale';
        $params = array(
            'openid'=>$openid,
        );
        $return =  $this->http($url,$params);
        return json_decode($return,true);
    }
    /**
     * 会员积分兑换
     */
    public function use_integral($openid,$user_id,$user_integral){
        $url = 'http://114.215.144.156:2233/api/use_integral';
        $params = array(
            'openid'=>$openid,
            'use_integral'=>$user_integral,
            'user_id'=>$user_id,
            'use_datetime'=>date('Y-m-d H:i:s'),
        );
        $return =  $this->http($url,$params);
        return json_decode($return,true);
    }

    /*
     * jifenjil1
     */
    public function integralrecord($openid){
        $url = 'http://114.215.144.156:2233/api/integralrecord';
        $params = array(
            'openid'=>$openid,
        );
        $return =  $this->http($url,$params);
        return json_decode($return,true);
    }
    /*
    * jifenjil1
    */
    public function orderid_vipsale($openid,$Order_id){
        $url = 'http://114.215.144.156:2233/api/orderid_vipsale';
        $params = array(
            'openid'=>$openid,
            'order_id'=>$Order_id,
        );
        $return =  $this->http($url,$params);
        return json_decode($return,true);
    }
    /*
   * jPEIZHI
   */
    public function update_configure($recharge_scale,$integral_scale){
        $url = 'http://114.215.144.156:2233/api/update_configure';
        $params = array(
            'recharge_scale'=>$recharge_scale,
            'integral_scale'=>$integral_scale,
        );
        $return =  $this->http($url,$params);
        return json_decode($return,true);
    }
    //CURL请求的函数http_request()
    public static function post2($url, $data){//file_get_content



        $postdata = http_build_query(

            $data

        );



        $opts = array('http' =>

            array(

                'method'  => 'POST',

                'header'  => 'Content-type: application/x-www-form-urlencoded',

                'content' => $postdata

            )

        );



        $context = stream_context_create($opts);


        $result = file_get_contents($url, false, $context);

        return $result;


    }

     /*
        * jifenjil1
        */
    public function query_picname($picname){
        $url = 'http://114.215.144.156:2233/api/query_picname';
        $params = array(
            'icecream_name'=>$picname,
        );
        $return =  $this->post2($url,$params);
        return json_decode($return,true);
    }

    /**
     * @param $phonelist
     * @param $content
     * @return mixe擦描述配置
     */
    public function canshu(){
        $url = 'http://114.215.144.156:2233/api/query_configure';
        $return =  $this->http($url);
        return json_decode($return,true);
    }
public function sendmobile($phonelist,$content){
    $url = 'http://sms.bamikeji.com:8890/mtPort/mt/normal/send';
    $params = array(
        'uid'=>1184,
        'passwd'=>'e10adc3949ba59abbe56e057f20f883e',
        'phonelist'=>$phonelist,
        'content'=>$content,
    );
    $return = $this->http($url,$params);
    return json_decode($return,true);
}

}