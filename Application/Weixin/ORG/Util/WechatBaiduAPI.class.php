<?php
namespace Home\ORG\Util;
class WechatBaiduAPI{
    private $api_server_url;
    private $auth_params;

    public function __construct()
    {
        $this->api_server_url = "http://apis.map.qq.com/uri/v1/search";
        $this->auth_params = array();
        $this->auth_params['ak'] = "37492c0ee6f924cb5e934fa08c6b1676";
    }

    //http://api.map.baidu.com/place/search?&query=眼镜&location=39.915,116.404&radius=3000&output=json&key=37492c0ee6f924cb5e934fa08c6b1676
    public function Place_search($query, $location, $radius=3000)
    {
        return $this->call("search", array("output" =>'json', "query" => $query,"page_size" =>10, "page_num" => 0,
            "scope" => 2,"location" => $location, "radius" => $radius));
    }

    protected function call($method, $params = array())
    {
        $params = array_merge($this->auth_params, $params);
        $url = $this->api_server_url . "$method?".http_build_query($params);
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

        $data = curl_exec($ch);
        curl_close($ch);

        $result = null;

        $result = json_decode($data,true);

        return $result;
    }
    /** * @desc 根据两点间的经纬度计算距离 * @param float $lat 纬度值 * @param float $lng 经度值 */
    function getDistance($lat1, $lng1, $lat2, $lng2) {
        $earthRadius = 6367000; //approximate radius of earth in meters
        //  /* Convert these degrees to radians to work with the formula */
        $lat1 = ($lat1 * pi() ) / 180; $lng1 = ($lng1 * pi() ) / 180;
        $lat2 = ($lat2 * pi() ) / 180; $lng2 = ($lng2 * pi() ) / 180;
        /* Using the Haversine formula  http://en.wikipedia.org/wiki/Haversine_formula  calculate the distance */
        $calcLongitude = $lng2 - $lng1; $calcLatitude = $lat2 - $lat1;
        $stepOne = pow(sin($calcLatitude / 2), 2) + cos($lat1) * cos($lat2) * pow(sin($calcLongitude / 2), 2);
        $stepTwo = 2 * asin(min(1, sqrt($stepOne))); $calculatedDistance = $earthRadius * $stepTwo;
        return round($calculatedDistance); }

}