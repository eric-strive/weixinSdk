<?php

namespace Home\ORG\weixin;
class templateNews
{
    public $appid;

    public $appsecret;

    public function __construct($appid = null, $appsecret = null)
    {

        $this->appid = $appid;
        $this->appsecret = $appsecret;
    }

    /**
     * @param $tempid
     * @param $tempKey
     * @param $openid
     * @param $dataArr
     * @param $color
     * @param $token
     */
    public function sendTempMsg($tempKey, $dataArr, $token, $color = null)
    {
        $requestUrl = 'https://api.weixin.qq.com/cgi-bin/message/template/send?access_token=' . $token;
        $data = $this->getData($tempKey, $dataArr);
        $tempdata = $this->templates($tempKey);
        $color = $color ? $color : $tempdata['color'];
        $sendData = '{"touser":"' . $dataArr['openid'] . '","template_id":"' . $tempdata["template_id"] . '","url":"' . $dataArr["href"] . '","topcolor":"' . $color . '","data":' . $data . '}';
        return $this->https_request($requestUrl, $sendData);
    }

    public function getData($key, $dataArr)
    {
        $tempsArr = $this->templates($key);
        $data = $tempsArr['vars'];
        $color = $tempsArr['color'];
        $data = array_flip($data);
        $jsonData = '';
        foreach ($dataArr as $k => $v) {
            if (in_array($k, array_flip($data))) {
                $jsonData .= '"' . $k . '":{"value":"' . $v . '","color":"' . $color . '"},';
            }
        }

        $jsonData = rtrim($jsonData, ',');

        return "{" . $jsonData . "}";
    }

    public function templates($tid)
    {
        $attr = array(
            'OPENTM406161651' => array(
                'name' => '下单成功提醒',
                'template_id' => 'Jun3LN746mj4ENXKekV_fGdeRIH-MOZMX_qySZILMp0',
                'vars' => array('first', 'keyword1', 'keyword2', 'keyword3', 'keyword4', 'remark'),
                'color' => '#173177'
            ),
            'OPENTM201743389' => array(
                'name' => '新订单提醒通知',
                'template_id' => 'IPsu8rn7VtUGpi94LsIXdiiCoYIfQ4ymYocWxkFoq04',
                'vars' => array('first', 'keyword1', 'keyword2', 'keyword3', 'keyword4', 'keyword5', 'remark'),
                'color' => '#173177'
            ),
            'OPENTM400504461' => array(
                'name' => '佣金提醒',
                'vars' => array('first', 'keyword1', 'keyword2', 'remark'),
                'color' => '#173177'
            ),
            'OPENTM407453584' => array(
                'name' => '帐号绑定提醒',
                'vars' => array('first', 'keyword1', 'keyword2', 'remark'),
                'color' => '#173177'
            ),
        );
        return $attr[$tid];
    }

    //CURL请求的函数http_request()
    function https_request($url, $data = null)
    {
        $curl = curl_init();
        curl_setopt($curl, CURLOPT_URL, $url);
        curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, FALSE);
        curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, FALSE);
        if (!empty($data)) {
            curl_setopt($curl, CURLOPT_POST, 1);
            curl_setopt($curl, CURLOPT_POSTFIELDS, $data);
        }
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
        $output = curl_exec($curl);
        curl_close($curl);
        return $output;
    }
}
