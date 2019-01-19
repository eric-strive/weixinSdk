/**
 * 载入XML文件并解析
 * @param  string xmlFile 文件路径
 * @return {[type]}         [description]
 */
function loadXML(xmlFile){
	var xmlDoc=null;
    //判断浏览器的类型
    //支持IE浏览器
    if(!window.DOMParser && window.ActiveXObject){
        var xmlDomVersions = ['MSXML.2.DOMDocument.6.0','MSXML.2.DOMDocument.3.0','Microsoft.XMLDOM'];
        for(var i=0;i<xmlDomVersions.length;i++){
            try{
                xmlDoc = new ActiveXObject(xmlDomVersions[i]);
                break;
            }catch(e){
            }
        }
    }
    //支持Mozilla浏览器
    else if(document.implementation && document.implementation.createDocument){
        try{
            /* document.implementation.createDocument('','',null); 方法的三个参数说明
             * 第一个参数是包含文档所使用的命名空间URI的字符串； 
             * 第二个参数是包含文档根元素名称的字符串； 
             * 第三个参数是要创建的文档类型（也称为doctype）
             */
            xmlDoc = document.implementation.createDocument('','',null);
        }catch(e){
        }
    }
    else{
        return null;
    }

    if(xmlDoc!=null){
        try{
            xmlDoc.async = false;
            xmlDoc.load(xmlFile);
        }catch(e){  //chrome的xmlDoc没有load属性,异步加载兼容
            try{
                var xmlhttp = new window.XMLHttpRequest();
                xmlhttp.open("GET",xmlFile,false);
                xmlhttp.send(null);
                xmlDoc = xmlhttp.responseXML;
            }catch(e){  //chrome的xmlDoc没有load属性,异步加载兼容
            }
        }
    }
    return xmlDoc;
}


function checkXMLDocObj(xmlFile){
	var xmlDoc = loadXML(xmlFile);
    if(xmlDoc==null){
        alert('您的浏览器不支持xml文件读取,于是本页面禁止您的操作,推荐使用IE5.0以上可以解决此问题!');
    }
    return xmlDoc;
}

/**
 * 获取下级地区，以对象nodeList形式返回
 * @param  int pid 父级地区id
 * @return {[type]}     [description]
 */
function get_addr_next(pid){
    var cons = thinkphp_path_parse();
    var addr = loadXML(cons.__PUBLIC__+"/xml/region.xml");  //"/wulv/Public/xml/region.xml"
    if(pid === undefined || pid == 0){
        var next = addr.documentElement.childNodes;
    }else{
        var next = addr.getElementsByTagName("_"+pid)[0].childNodes;
    } 
    //alert(next[2].getAttribute("name"));
    return next;
}

/**
 * 获取地区id对应的地区名称
 * @param  int id 地区id
 * @return {[type]}    [description]
 */
function get_addr_name_byid(id){
    var cons = thinkphp_path_parse();
    var addr = loadXML(cons.__PUBLIC__+"/xml/region.xml");  //"/wulv/Public/xml/region.xml"
    var name = addr.getElementsByTagName("_"+id)[0].getAttribute('name');
    //alert(next);
    return name;
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


var s = ["province","city","county"];//三个select的name
var init_address = [
    "<option value='' pid=''>请选择省</option>",
    "<option value='' pid=''>请选择市</option>",
    "<option value='' pid=''>请选择区</option>",
];
var address = ['','',''];
function _init_area(s,value){  //初始化函数
    for(i=0;i<s.length-1;i++){
        $('#'+s[i]).change(new Function("change("+(i)+",['"+s[0]+"','"+s[1]+"','"+s[2]+"'])"));
    }
    address = value ? value : address;
    areaItems(0,s);
}

function change(v,s){
    var ss = $('#'+s[v]);
    areaItems(ss.find(':selected').attr('pid'),s);
}

function areaItems(parentId,s){
    var areaarr = get_addr_next(parentId);
    if(areaarr[0] != undefined){
        var level = areaarr[0].getAttribute("level");
    }
    var html = init_address[level-1];
    var html_city = init_address[1];
    var html_county = init_address[2];
    if(level == 1){ 
        $("#"+s[1]).html(html_city);     
        $("#"+s[2]).html(html_county);  
        for (var i = 0,len=areaarr.length; i<len; i++) {
            var area_name = areaarr[i].getAttribute("name");
            var area_id = areaarr[i].getAttribute("id");            
            html += '<option value="'+area_name+'" pid="'+area_id+'"';
            if(address[0] && area_name == address[0]){
                html += 'selected="selected"';
                areaItems(area_id,s);
            }
            html += ' >'+area_name+'</option>';
        };        
    }else if(level == 2){
        $("#"+s[2]).html(html_county);
        for (var i = 0,len=areaarr.length; i<len; i++) {
            var area_name = areaarr[i].getAttribute("name");
            var area_id = areaarr[i].getAttribute("id");
            html += '<option value="'+area_name+'" pid="'+area_id+'"';
            if(address[1] && area_name == address[1]){
                html += 'selected="selected"';
                areaItems(area_id,s);
            }
            html += ' >'+area_name+'</option>';
        };                
    }else{
        for (var i = 0,len=areaarr.length; i<len; i++) {
            var area_name = areaarr[i].getAttribute("name");
            var area_id = areaarr[i].getAttribute("id");
            html += '<option value="'+area_name+'" pid="'+area_id+'"';
            if(address[2] && area_name == address[2]){
                html += 'selected="selected"';
            }
            html += '>'+area_name+'</li>';
        }
    }
    $("#"+s[level-1]).html(html);
    
}