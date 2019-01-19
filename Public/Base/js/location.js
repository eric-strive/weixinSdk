	/*
	调用该函数需要满足条件：
	1.在之前加入
	<script type="text/javascript" src="http://api.map.baidu.com/api?v=2.0&ak=kp2hG52scQzvruo3CS2KgkZi"></script>
	<script type="text/javascript" src="http://developer.baidu.com/map/jsdemo/demo/convertor.js"></script>
	2.在之前加入
	<div id="allmap"></div>
	3.加do_result(result) 函数
		在do_result中执行得到距离后的操作
	*/

/**
 * 获取当前位置与店铺的距离
 * @param  {[type]} loc_x 店铺x坐标
 * @param  {[type]} loc_y 店铺y坐标
 * @param  {[type]} local 是否开启同城显示 true：在同一城市才返回距离 false：无论在哪都返回距离;默认为false
 * @return {[type]}  [description]
 */
function get_distance(loc_x,loc_y,local){
    var local = arguments[2] ? arguments[2] : false; 
	if (navigator.geolocation){
        navigator.geolocation.getCurrentPosition(showPosition);
    }else{
        alert("浏览器不支持Geolocation");
    }

    function showPosition(position){ 
        var lx= position.coords.longitude;          //真实坐标 (需要转换为 百度坐标)
        var ly= position.coords.latitude; 
        var gpsPoint = new BMap.Point(lx,ly);
    //地图初始化
        var bm = new BMap.Map("allmap");
        bm.addControl(new BMap.NavigationControl());
    //坐标转换完之后的回调函数
        translateCallback = function(point){
        	var cur_x = point.lng;
            var cur_y = point.lat;
            var cur_city = '';
            var pointB = new BMap.Point(loc_x,loc_y);   
            var gc = new BMap.Geocoder();
            gc.getLocation(pointB,function(res){
                cur_city = res.addressComponents.city;
            })
            gc.getLocation(point, function(rs){
                var addComp = rs.addressComponents;
                var city = addComp.city; 
                var cons = thinkphp_path_parse();
                $.ajax({
                    type:"POST",
                    url:cons.__APP__+"/Nearby/set_location",
                    data:{"cur_x":cur_x,"cur_y":cur_y,"city":city},
                })
                if(local==true){
                    if(cur_city==city){             
                        var pointA = new BMap.Point(cur_x,cur_y);
                        var distance = (bm.getDistance(pointA,pointB)).toFixed(2);
                        if(distance<1000){
                            result = distance+'米';
                            do_result(result);
                        }else if(distance>=1000){
                            distance = (distance/1000).toFixed(2);
                            result = distance+'公里';
                            do_result(result);
                        } 
                    }else{
                        result = "";
                        do_result(result);
                    }
                }else{
                    var pointA = new BMap.Point(cur_x,cur_y);
                    var distance = (bm.getDistance(pointA,pointB)).toFixed(2);
                    if(distance<1000){
                        result = distance+'米';
                        do_result(result);
                    }else if(distance>=1000){
                        distance = (distance/1000).toFixed(2);
                        result = distance+'公里';
                        do_result(result);
                    } 
                }
                
            })
        } 
        BMap.Convertor.translate(gpsPoint,0,translateCallback);     //真实经纬度转成百度坐标
	}   
	

    /**
     * 仿thinkphp的js路径解析
     * @return {[type]} [description]
     */
    function thinkphp_path_parse(){
        var url= window.location.href;
        var reg = /(((.*)\/[a-zA-Z]+\.php)(\/*[a-zA-Z]*))(\/*[a-zA-Z]*)/;
        var match = url.match(reg);

        var __ROOT__ = match[3];
        var __APP__ = match[2];
        var __URL__ = match[4] ? match[1] : match[1] + "/Index";
        var __ACTION__ = match[5] ? match[0] : __URL__ + "/index";
        var __PUBLIC__ = __ROOT__ + "/Public";

        return {"__ROOT__":__ROOT__, "__APP__":__APP__, "__URL__":__URL__, "__ACTION__":__ACTION__, "__PUBLIC__":__PUBLIC__};
    }
}

